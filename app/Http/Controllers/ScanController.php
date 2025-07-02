<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modality;
use App\Models\RequestListing;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;
use Stripe\PaymentMethod;
use Stripe\Customer;
use App\Models\File;
use App\Models\User;
use App\Notifications\PaymentReceived;
use Illuminate\Support\Facades\Log;

/**
 * ScanController handles radiology scan uploads, payment processing, and form management.
 * Integrates with Stripe for payment processing and provides chunked file upload functionality.
 */
class ScanController extends Controller
{
    /**
     * Display the scan upload form with modalities and Stripe integration.
     * Generates unique UUID for the request and loads available modalities.
     */
    public function scanupload()
    {
        // Get all available modalities for selection
        $modalities = Modality::all();

        // Get Stripe publishable key for frontend integration
        $stripeKey = env('STRIPE_KEY');
        
        // Generate unique UUID for this request
        $uuid = (string) Str::uuid();

        return view('scan_page.index', compact('modalities', 'stripeKey', 'uuid'));
    }

    /**
     * Process initial scan form submission and create request listing.
     * Handles date format conversion and exam ID generation.
     */
    public function uploadform(Request $request)
    {
        // Convert date formats from d/m/Y to Y-m-d for database storage
        $dob = $request->patient_dob ? Carbon::createFromFormat('d/m/Y', $request->patient_dob)->format('Y-m-d') : null;
        $scanDate = $request->scan_date ? Carbon::createFromFormat('d/m/Y', $request->scan_date)->format('Y-m-d') : null;
        $appointment = $request->appointment ? Carbon::createFromFormat('d/m/Y', $request->appointment)->format('Y-m-d') : null;

        // Merge reformatted dates into request
        $request->merge([
            'patient_dob' => $dob,
            'scan_date' => $scanDate,
            'appointment' => $appointment,
        ]);

        // Validate incoming request data
        $validated = $request->validate([
            'uuid' => 'required|string',
            'patient_name' => 'required|string',
            'patient_dob' => 'required|date',
            'appointment' => 'required|date',
            'patient_phone' => 'required|regex:/^[0-9]{1,15}$/',
            'patient_postcode' => 'required|string',
            'patient_address' => 'required|string',
            'patient_email' => 'required|string',
            'clinical_history' => 'required|string',
            'question' => 'required|string',
            'scan_date' => 'nullable|date',
            'scan_file' => 'nullable|string',
            'modality' => 'nullable|string',
        ]);

        // Get modality and calculate payment amount
        $modality = Modality::find($validated['modality']);
        $paymentAmount = $modality ? $modality->price : null;

        // Generate unique exam ID with FUS prefix
        $baseId = 'FUS' . str_pad(mt_rand(1000, 99999), 5, '0', STR_PAD_LEFT);
        $examId = $baseId;

        // Ensure exam ID uniqueness by adding random letter if duplicate
        if (RequestListing::where('exam_id', $examId)->exists()) {
            $randomLetter = chr(mt_rand(65, 90)); // A-Z
            $examId = 'FUS' . $randomLetter . substr($baseId, 3);
        }

        // Create or update request listing
        $requestListing = RequestListing::updateOrCreate(['uuid' => $request->uuid], [
            'user_id' => Auth::id(),
            'exam_id' => $examId,
            'patient_name' => $request->patient_name,
            'patient_dob' => $dob,
            'appointment' => $appointment,
            'patient_phone' => $request->patient_phone,
            'patient_postcode' => $request->patient_postcode,
            'patient_address' => $request->patient_address,
            'patient_email' => $request->patient_email,
            'clinical_history' => $request->clinical_history,
            'question' => $request->question,
            'scan_date' => $scanDate,
            'modality' => $request->modality,
            'scan_file' => $request->scan_file,
        ]);

        $requestListing->save();

        // Return response based on request type
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('scan.upload.page', ['uuid' => $request->uuid])->with('success', 'Scan details uploaded.');
    }

    /**
     * Display scan upload edit form with existing data and payment methods.
     * Loads user's saved payment methods and creates setup intent for new cards.
     */
    public function scanuploadedit($uuid)
    {
        // Load request listing with user relationship
        $requestListing = RequestListing::where('uuid', $uuid)->with('user')->firstOrFail();
        $user = $requestListing->user;
        
        // Get associated files and define file types
        $files = File::where('request_uuid', $uuid)->get();

        $types = [
            "patients_docs",
            "patients_supporting_files",
            "doctors_docs",
            "doctors_supporting_files"
        ];

        // Initialize Stripe with secret key
        $stripeSecret = env('STRIPE_SECRET');
        Stripe::setApiKey($stripeSecret);

        $paymentMethods = [];

        // Get user's saved payment methods if they have a Stripe customer ID
        if ($user && $user->stripe_customer_id) {
            $paymentMethods = PaymentMethod::all([
                'customer' => $user->stripe_customer_id,
                'type' => 'card',
            ]);
        } 

        // Create setup intent for adding new payment methods
        $setupIntent = null;
        if ($user && $user->stripe_customer_id) {
            $setupIntent = \Stripe\SetupIntent::create([
                'customer' => $user->stripe_customer_id,
            ]);
        } else {
            $setupIntent = \Stripe\SetupIntent::create(); 
        }

        // Load modalities and get Stripe publishable key
        $modalities = Modality::all();
        $stripeKey = env('STRIPE_KEY');

        return view('request_listing.edit', compact('requestListing', 'modalities', 'stripeKey', 'files','paymentMethods', 'setupIntent'));
    }

    /**
     * Update scan form data for existing requests.
     * Prevents updates after payment completion and handles date conversions.
     */
    public function uploadformupdate(Request $request, $uuid)
    {
        // Fetch the request by UUID
        $requestListing = RequestListing::where('uuid', $uuid)->first();

        if (!$requestListing) {
            Log::warning("Request not found for UUID: {$uuid}");
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        // Prevent updates after payment is completed
        if ($requestListing->payment_status === 'paid') {
            Log::info("Update attempt blocked for paid request: {$uuid}");
            return response()->json([
                'success' => false,
                'message' => 'You cannot update the form after payment is completed.'
            ], 403);
        }

        // Convert date formats from d/m/Y to Y-m-d
        try {
            $dob = $request->patient_dob_update ? Carbon::createFromFormat('d/m/Y', $request->patient_dob_update)->format('Y-m-d') : null;
            $scanDate = $request->scan_date_update ? Carbon::createFromFormat('d/m/Y', $request->scan_date_update)->format('Y-m-d') : null;
            $appointment = $request->appointment ? Carbon::createFromFormat('d/m/Y', $request->appointment)->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format.'], 422);
        }

        // Merge reformatted dates into request
        $request->merge([
            'patient_dob_update' => $dob,
            'scan_date_update' => $scanDate,
            'appointment' => $appointment,
        ]);

        // Validate input data
        try {
            $validated = $request->validate([
                'patient_name_update' => 'required|string',
                'patient_dob_update' => 'required|date',
                'appointment' => 'required|date',
                'patient_phone' => 'required|regex:/^[0-9]{1,15}$/',
                'patient_postcode' => 'required|string',
                'patient_address' => 'required|string',
                'patient_email' => 'required|string',
                'clinical_history' => 'required|string',
                'question' => 'required|string',
                'scan_date_update' => 'nullable|date',
                'scan_file_update' => 'nullable|string',
                'modality_update' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // re-throw to allow Laravel to handle it as normal
        }

        // Get modality and calculate payment amount
        $modality = Modality::where('name', $validated['modality_update'])->first();
        $paymentAmount = $modality ? $modality->price : null;

        // Update request listing fields
        $requestListing->patient_name = $validated['patient_name_update'];
        $requestListing->patient_dob = $dob;
        $requestListing->scan_date = $scanDate;
        $requestListing->appointment = $validated['appointment'];
        $requestListing->patient_phone = $validated['patient_phone'];
        $requestListing->patient_postcode = $validated['patient_postcode'];
        $requestListing->patient_address = $validated['patient_address'];
        $requestListing->patient_email = $validated['patient_email'];
        $requestListing->clinical_history = $validated['clinical_history'];
        $requestListing->question = $validated['question'];
        $requestListing->modality = $validated['modality_update'];
        $requestListing->scan_file = $validated['scan_file_update'];
        $requestListing->save();

        // Return response based on request type
        $responseMessage = 'Scan updated successfully.';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $responseMessage]);
        } else {
            return redirect()->route('scan.upload.page', ['uuid' => $uuid])->with('success', $responseMessage);
        }
    }

    /**
     * Create Stripe setup intent for adding new payment methods.
     * Ensures user has Stripe customer ID and creates setup intent for frontend.
     */
    public function createSetupIntent(Request $request)
    {
        $user = auth()->user();

        // Initialize Stripe with secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create Stripe customer if user doesn't have one
        if (!$user->stripe_customer_id) {
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name'  => $user->username,
                'metadata' => [
                    'uuid'    => $user->uuid,
                ],
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        } else {
            $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
        }

        // Create setup intent for adding new payment methods
        $setupIntent = SetupIntent::create([
            'customer' => $user->stripe_customer_id,
            'payment_method_types' => ['card'],
        ]);

        return response()->json([
            'client_secret' => $setupIntent->client_secret,
        ]);
    }

    /**
     * Process payment using Stripe PaymentIntent API.
     * Handles card saving, payment processing, and notification sending.
     */
    public function ajaxCharge(Request $request)
    {
        $uuid = $request->uuid;
        $requestListing = RequestListing::where('uuid', $uuid)->first();

        if (!$requestListing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request listing not found.',
            ]);
        }
        
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $user = $requestListing->user;

            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name'  => $user->username,
                    'metadata' => [
                        'uuid'    => $user->uuid,
                    ],
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            } else {
                $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);

                // Update customer details in Stripe
                \Stripe\Customer::update($customer->id, [
                    'email' => $user->email,
                    'name'  => $user->username,
                    'metadata' => [
                        'uuid' => $user->uuid,
                    ],
                ]);
            }

            $paymentMethodId = $request->payment_method_id ?? null;

            if (!$paymentMethodId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No payment method provided.',
                ]);
            }

            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $saveCard = $request->boolean('save_card');

            // Handle card saving logic
            if ($saveCard) {
                $newFingerprint = $paymentMethod->card->fingerprint;
                $existingMethods = \Stripe\PaymentMethod::all([
                    'customer' => $customer->id,
                    'type' => 'card',
                ]);

                // Check for duplicate cards by fingerprint
                $duplicate = collect($existingMethods->data)->first(function ($existingMethod) use ($newFingerprint) {
                    return $existingMethod->card->fingerprint === $newFingerprint;
                });

                if ($duplicate) {
                    $paymentMethodId = $duplicate->id;
                } else {
                    // Attach new payment method to customer
                    if ($paymentMethod->customer !== $customer->id) {
                        $paymentMethod->attach(['customer' => $customer->id]);
                        \Stripe\Customer::update($customer->id, [
                            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
                        ]);
                    }
                }
            }

            // Create PaymentIntent for payment processing
            $intent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'gbp',
                'customer' => $customer->id,
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
                'description' => 'Modality: ' . $request->modality_name,
            ]);

            // Update request listing with payment details
            $requestListing->payment_amount = $request->amount / 100;
            $requestListing->payment_status = 'unpaid';
            $requestListing->status = 'Assigned';
            $requestListing->save();

            // Create payment record
            Payment::create([
                'request_listing_id' => $requestListing->id,
                'stripe_charge_id' => $intent->charges->data[0]->id ?? null,
                'amount' => $request->amount / 100,
                'currency' => 'gbp',
                'status' => $intent->status,
                'payment_method' => $intent->payment_method,
                'receipt_url' => $intent->charges->data[0]->receipt_url ?? null,
                'paid_at' => now(),
            ]);

            // Notify admin users about payment
            $adminUsers = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new PaymentReceived($requestListing));
            }

            return response()->json([
                'status' => 'success',
                'charge_id' => $intent->charges->data[0]->id ?? null,
                'redirect_url' => route('thank.you', ['isrequest' => 'RequestSubmitted']),
            ]);
        } catch (\Exception $e) {
            // Handle payment failure
            $requestListing->payment_status = 'unpaid';
            $requestListing->save();

            Payment::create([
                'request_listing_id' => $requestListing->id,
                'amount' => $request->amount / 100,
                'stripe_charge_id' => $intent->charges->data[0]->id ?? null,
                'currency' => 'gbp',
                'status' => 'failed',
                'payment_method' => $paymentMethodId ?? 'unknown',
                'failure_message' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle chunked file uploads for large scan files.
     * Supports progress tracking and handles both partial and complete uploads.
     */
    public function upload(Request $request) {
        // Create file receiver for chunked uploads
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // Check if upload is successful
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // Receive and process uploaded file chunks
        $save = $receiver->receive();

        // Check if upload is complete
        if ($save->isFinished()) {
            // Save complete file and return response
            return $this->saveFile($save->getFile());
        }

        // Get upload handler for progress tracking
        $handler = $save->handler();

        // Return upload progress percentage
        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }

    /**
     * Save uploaded file to storage and return file information.
     * Creates unique filename and stores file in private uploads directory.
     */
    protected function saveFile(UploadedFile $file)
    {
        // Generate unique filename
        $fileName = $this->createFilename($file);

        // Define file storage path
        $filePath = "private/uploads/";
        $finalPath = storage_path("app/" . $filePath);

        // Create directory if it doesn't exist
        if (!file_exists($finalPath)) {
            mkdir($finalPath, 0777, true);
        }

        // Move uploaded file to storage
        $file->move($finalPath, $fileName);

        // Get MIME type safely (after move)
        $mimeType = mime_content_type($finalPath . '/' . $fileName);

        return response()->json([
            'path' => $filePath,
            'name' => $fileName,
            'mime_type' => $mimeType,
        ]);
    }

    /**
     * Generate unique filename to prevent conflicts.
     * Adds timestamp hash to original filename.
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to make filename unique
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }

    /**
     * Remove uploaded scan file from storage and database.
     * Handles file deletion, database cleanup, and request listing updates.
     */
    public function removeUploadedFileScan(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'file_path' => 'required|string',
            'request_uuid' => 'nullable|uuid',
        ]);

        $filePath = $validated['file_path']; // Example: private/uploads/filename.pdf
        $requestUuid = $validated['request_uuid'] ?? null;
        $fullPath = storage_path("app/" . $filePath);
        $fileName = basename($filePath); // Only filename, for DB matching

        // Check if file exists and delete it
        if (file_exists($fullPath)) {
            if (unlink($fullPath)) {
                // Remove file record from database
                $query = File::where('file_url', 'like', "%{$fileName}%");

                if ($requestUuid) {
                    $query->where('request_uuid', $requestUuid);
                }

                $query->delete();

                // Update request listing scan_file field if UUID provided
                if ($requestUuid) {
                    RequestListing::where('uuid', $requestUuid)
                        ->where('scan_file', 'like', "%{$fileName}")
                        ->update(['scan_file' => null]);
                }

                return response()->json(['success' => true, 'message' => 'File removed successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'File could not be deleted'], 500);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }
    }
}
