<?php

namespace App\Http\Controllers;

use App\Models\RequestListing;
use App\Models\File;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use App\Models\User;
use App\Notifications\RequestStatusUpdated;
use App\Notifications\NotesStatusUpdated;
use Illuminate\Support\Facades\Validator;
use App\Models\NotesHistory;
use App\Models\StatusHistory;
use App\Notifications\StatusChangeRequested;
use App\Notifications\StatusChangeApproved;
use App\Notifications\StatusChangeRejected;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Notifications\NotesChangeRequested;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * RequestListingController handles radiology request management, status updates, and report generation.
 * Provides role-based access control for admin, sub-admin, and user operations.
 */
class RequestListingController extends Controller
{
    // Commented out original index method
    // public function index()
    // {
    //     if (request()->ajax()) {
    //         $request_listing = RequestListing::with('user')
    //             ->where('payment_status', 'paid'); // select columns where amount is paid from request_listings

    //         return DataTables::of($request_listing)
    //             ->addIndexColumn()
    //             ->addColumn('username', function($row) {
    //                 return $row->user ? $row->user->username : 'N/A';
    //             })
    //             ->addColumn('action', function ($row) {
    //                 return '<a href="' . route('request-listing.view', $row->uuid) . '" class="btn btn-info btn-sm">View</a>';
    //             })
    //             ->addColumn('status', function($row) {
    //                 $status = $row->status;

    //                 // Sanitize status to be a valid CSS class name:
    //                 // Replace spaces and & with underscore
    //                 $class = str_replace([' ', '&'], '_', $status);

    //                 return '<span class="status ' . htmlspecialchars($class) . '">' . htmlspecialchars($status) . '</span>';
    //             })
    //             ->rawColumns(['status', 'action'])
    //             ->make(true);
    //     }

    //     return view('request_listing.index');
    // }

    /**
     * Display admin view of all paid radiology requests with filtering and search.
     * Provides DataTables integration with status filtering and search functionality.
     */
    public function index()
    {
        if (request()->ajax()) {
            $status = request('status'); // Get status filter from request
            $searchValue = request('searchValue'); // Get search input value

            // Build query for paid requests with user relationship
            $query = RequestListing::with('user')->where('payment_status', 'paid');

            // Filter by status if provided (exclude 'All' option)
            if ($status && $status !== 'All') {
                $query->where('status', $status);
            }
            
            // Apply search filter across patient name and username
            if ($searchValue) {
                $query->where(function($q) use ($searchValue) {
                    $q->Where('patient_name', 'like', "%{$searchValue}%")
                    ->orWhereHas('user', function ($q2) use ($searchValue) {
                        $q2->where('username', 'like', "%{$searchValue}%");
                    });
                });
            }
            
            // Handle DataTables ordering
            $order = request('order');
            $columns = request('columns');

            if (!$order || empty($order)) {
                // Default ordering by creation date (newest first)
                $query->orderBy('created_at', 'desc');
            } else {
                $orderColumnIndex = $order[0]['column'];
                $orderDir = $order[0]['dir'];
                $orderColumnName = $columns[$orderColumnIndex]['data'] ?? null;

                // Override default first column ascending with created_at descending
                if ($orderColumnIndex == 0 && $orderDir == 'asc') {
                    $query->orderBy('created_at', 'desc');
                } else {
                    // Let DataTables handle ordering normally
                    $query->orderBy($orderColumnName, $orderDir);
                }
            }

            // Return DataTables response with custom columns
            return DataTables::of($query)
                ->addIndexColumn()
               
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('request-listing.view', $row->uuid) . '" class="btn btn-info btn-sm">View</a>
                            ';
                })
                ->addColumn('status', function($row) {
                    $status = $row->status;
                    // Sanitize status for CSS class names
                    $class = str_replace([' ', '&'], '_', $status);
                    return '<span class="status ' . htmlspecialchars($class) . '">' . htmlspecialchars($status) . '</span>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('request_listing.index');
    }

    /**
     * Display user-specific view of their radiology requests.
     * Shows only requests belonging to the authenticated user with payment status handling.
     */
    public function indexuser()
    {
        if (request()->ajax()) {
            $status = request('status'); // Get status filter from request
            $searchValue = request('searchValue'); // Get search input value

            // Build query for user's own requests
            $query = RequestListing::with('user')
                    ->where('user_id', Auth::id());

            // Filter by status if provided
            if ($status && $status !== 'All') {
                $query->where('status', $status);
            }
            
            // Apply search filter across patient name and username
            if ($searchValue) {
                $query->where(function($q) use ($searchValue) {
                    $q->Where('patient_name', 'like', "%{$searchValue}%")
                    ->orWhereHas('user', function ($q2) use ($searchValue) {
                        $q2->where('username', 'like', "%{$searchValue}%");
                    });
                });
            }

            // Handle DataTables ordering (same logic as admin index)
            $order = request('order');
            $columns = request('columns');

            if (!$order || empty($order)) {
                $query->orderBy('created_at', 'desc');
            } else {
                $orderColumnIndex = $order[0]['column'];
                $orderDir = $order[0]['dir'];
                $orderColumnName = $columns[$orderColumnIndex]['data'] ?? null;

                if ($orderColumnIndex == 0 && $orderDir == 'asc') {
                    $query->orderBy('created_at', 'desc');
                } else {
                    $query->orderBy($orderColumnName, $orderDir);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                // Commented out username column
                // ->addColumn('username', function($row) {
                //     return $row->user ? $row->user->username : 'N/A';
                // })
                ->addColumn('action', function ($row) {
                        $viewBtn = '';
                        // Show view button only for paid requests
                        if ($row->payment_status == 'paid') {
                        $viewBtn = '<a href="' . route('request-listing.view', $row->uuid) . '" class="btn btn-info btn-sm">View</a>';
                        }
    
                        $editBtn = '';
                        // Show payment button for unpaid requests
                        if ($row->payment_status !== 'paid') {
                            $editBtn = '<a href="' . route('scan.upload.page', $row->uuid) . '" class="btn btn-warning btn-sm">Make Payment</a>';
                        }
                        return $viewBtn . ' ' . $editBtn;
                })
                ->addColumn('status', function($row) {
                    $status = $row->status;
                    $class = str_replace([' ', '&'], '_', $status);
                    return '<span class="status ' . htmlspecialchars($class) . '">' . htmlspecialchars($status) . '</span>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('request_listing.indexuser');
    }

    /**
     * View detailed information of a specific radiology request.
     * Handles role-based access control and admin token authentication.
     */
    public function view($uuid)
    {
        $authUser = auth()->user();

        // Build query with user and status history relationships
        $query = RequestListing::with(['user', 'statusHistory.updatedBy'])
            ->where('uuid', $uuid);

        // Restrict access: users can only view their own requests unless admin/sub-admin
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin')) {
            $query->where('user_id', $authUser->id);
        }

        $request = $query->firstOrFail();

        // Handle unauthenticated access with token verification
        if (!auth()->check() && !request()->has('token')) {
            return redirect()->route('login');
        }

        // Token-based admin access for sharing links
        if (!auth()->check() && request()->has('token')) {
            $token = request('token');
            if ($request->admin_access_token === $token && 
                $request->token_expires_at && 
                $request->token_expires_at->isFuture()) {
                // Valid token: find and log in admin user
                $admin = User::role('admin')->first();
                if ($admin) {
                    auth()->login($admin);
                    // Clear token after successful login
                    $request->update([
                        'admin_access_token' => null,
                        'token_expires_at' => null
                    ]);
                } else {
                    return redirect()->route('login')->with('error', 'No admin user found.');
                }
            } else {
                // Invalid or expired token
                return redirect()->route('login')->with('error', 'Invalid or expired access link.');
            }
        }

        // Generate new admin access token for authenticated admins
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            $token = Str::random(64);
            $request->update([
                'admin_access_token' => $token,
                'token_expires_at' => now()->addHours(24)
            ]);
        }

        // Get associated files and define file types
        $files = File::where('request_uuid', $uuid)->get();

        $types = [
            "patients_docs",
            "patients_supporting_files",
            "doctors_docs",
            "doctors_supporting_files"
        ];

        return view('request_listing.view', compact('request', 'files', 'types'));
    }

    /**
     * Display chat interface for a specific request.
     */
    public function chat($uuid)
    {
        $request = RequestListing::where('uuid', $uuid)->firstOrFail();
        return view('request_listing.chat', compact('request', "uuid"));
    }

    /**
     * Update notes and handle approval/rejection workflow.
     * Implements role-based workflow where sub-admins submit for admin approval.
     */
    public function updateStatusNotes(Request $request, $uuid)
    {
        $authUser = auth()->user();
        $requestListing = RequestListing::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization: only admin, sub-admin, or request owner can update
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin') && $requestListing->user_id !== $authUser->id) {
            abort(404);
        }
        
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string',
            'rejection_comment' => 'nullable|string',
            'approval_comment' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        
        // Admin workflow: can approve or reject notes
        if ($user->hasRole('admin')) {
            if ($request->status === 'rejected') {
                // Reject notes with rejection details
                $requestListing->update([
                    'notes_status' => 'rejected',
                    'rejection_comment' => $request->rejection_comment,
                    'rejected_by' => $user->id,
                    'rejected_at' => now(),
                ]);

                // Record rejection in history
                NotesHistory::create([
                    'request_uuid' => $requestListing->uuid,
                    'notes_content' => $requestListing->notes ?? $requestListing->pending_notes,
                    'status' => 'rejected',
                    'comment' => $request->rejection_comment,
                    'updated_by' => $user->id
                ]);

                // Notify sub-admin who made the changes
                if ($requestListing->notes_updated_by) {
                    $subAdmin = User::find($requestListing->notes_updated_by);
                    if ($subAdmin) {
                        $subAdmin->notify(new NotesStatusUpdated($requestListing, 'rejected', $request->rejection_comment));
                    }
                }
            } else {
                // Approve notes and make them active
                $requestListing->update([
                    'notes' => $request->notes,
                    'notes_status' => 'approved',
                    'notes_approved_by' => $user->id,
                    'notes_approved_at' => now(),
                    'approval_comment' => $request->approval_comment,
                    'pending_notes' => null, // Clear pending notes
                ]);

                // Record approval in history
                NotesHistory::create([
                    'request_uuid' => $requestListing->uuid,
                    'notes_content' => $request->notes,
                    'status' => 'approved',
                    'comment' => $request->approval_comment,
                    'updated_by' => $user->id
                ]);

                // Notify sub-admin who made the changes
                if ($requestListing->notes_updated_by) {
                    $subAdmin = User::find($requestListing->notes_updated_by);
                    if ($subAdmin) {
                        $subAdmin->notify(new NotesStatusUpdated($requestListing, 'approved', $request->approval_comment));
                    }
                }
            }
        } else {
            // Sub-admin workflow: submit notes for approval
            $requestListing->update([
                'pending_notes' => $request->notes,
                'notes_status' => 'pending',
                'notes_updated_by' => $user->id,
                'notes_updated_at' => now(),
            ]);

            // Record pending submission in history
            NotesHistory::create([
                'request_uuid' => $requestListing->uuid,
                'notes_content' => $request->notes,
                'status' => 'pending',
                'updated_by' => $user->id
            ]);

            // Notify all admins about pending notes
            $admins = User::role('admin')->get();
            Log::info('Sending notifications to admins', [
                'admin_count' => $admins->count(),
                'request_uuid' => $requestListing->uuid
            ]);
            
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new NotesChangeRequested($requestListing, $user));
                    Log::info('Notification sent to admin', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send notification to admin', [
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Notes updated successfully.');
    }

    /**
     * Update request status with role-based approval workflow.
     * Admins can update directly, sub-admins require approval.
     */
    public function updateStatus(Request $request, $uuid)
    {
        $authUser = auth()->user();
        $requestListing = RequestListing::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin') && $requestListing->user_id !== $authUser->id) {
            abort(404);
        }
        
        $requestListing = RequestListing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Admin workflow: direct status update
        if ($user->hasRole('admin')) {
            $requestListing->update([
                'status' => $request->status,
                'pending_status' => false,
                'status_updated_by' => $user->id,
                'status_updated_at' => now(),
            ]);

            // Record status change in history
            StatusHistory::create([
                'request_uuid' => $requestListing->uuid,
                'previous_status' => $requestListing->getOriginal('status'),
                'new_status' => $request->status,
                'status' => $request->status ?? 'pending',
                'updated_by' => $user->id
            ]);

            // Notify all sub-admins about status change
            $subAdmins = User::role('sub-admin')->get();
            foreach ($subAdmins as $subAdmin) {
                $subAdmin->notify(new RequestStatusUpdated($requestListing));
            }
        } else {
            // Sub-admin workflow: submit status change for approval
            $requestListing->update([
                'pending_status' => true,
                'pending_status_value' => $request->status,
                'status_updated_by' => $user->id,
                'status_updated_at' => now(),
            ]);

            // Record pending status change in history
            StatusHistory::create([
                'request_uuid' => $requestListing->uuid,
                'previous_status' => $requestListing->status,
                'new_status' => $request->status,
                'status' => $request->status ?? 'pending',
                'updated_by' => $user->id
            ]);

            // Notify admins about pending status change
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new StatusChangeRequested($requestListing, $user, $request->status));
            }
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    /**
     * Handle admin approval or rejection of status change requests from sub-admins.
     */
    public function approveStatusChange(Request $request, $uuid)
    {
        $authUser = auth()->user();
        $requestListing = RequestListing::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin') && $requestListing->user_id !== $authUser->id) {
            abort(404);
        }
        
        $requestListing = RequestListing::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();

        // Only admins can approve/reject status changes
        if (!$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($request->action === 'approve') {
            // Approve the pending status change
            $requestListing->update([
                'status' => $requestListing->pending_status_value,
                'status_approved_by' => $user->id,
                'status_approved_at' => now(),
                'pending_status' => null,
                'status_approved_comment' => $request->approval_comment,
            ]);

            // Record approved status change in history
            StatusHistory::create([
                'request_uuid' => $requestListing->uuid,
                'status' => $requestListing->pending_status_value,
                'updated_by' => $user->id
            ]);

            // Notify sub-admin who requested the change
            if ($requestListing->status_updated_by) {
                $subAdmin = User::find($requestListing->status_updated_by);
                if ($subAdmin) {
                    $subAdmin->notify(new StatusChangeApproved($requestListing, $user));
                }
            }
        } else {
            // Reject the pending status change
            $requestListing->update([
                'pending_status' => null,
                'status_rejection_comment' => $request->rejection_comment,
                'status_rejected_by' => auth()->id(),
                'status_rejected_at' => now(),
            ]);

            // Notify sub-admin who requested the change
            if ($requestListing->status_updated_by) {
                $subAdmin = User::find($requestListing->status_updated_by);
                if ($subAdmin) {
                    $subAdmin->notify(new StatusChangeRejected($requestListing, $user, $request->rejection_comment));
                }
            }
        }

        return redirect()->back()->with('success', 'Status change request processed successfully.');
    }

    /**
     * Update doctor notes for a request.
     * Allows admin, sub-admin, or request owner to update clinical notes.
     */
    public function updateStatusDoctorNotes(Request $request, $uuid)
    {
        $authUser = auth()->user();
        $listing = RequestListing::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin') && $listing->user_id !== $authUser->id) {
            abort(404);
        }

        // Validate doctor notes
        $request->validate([
            'doctor_notes' => 'nullable|string',
        ]);

        // Update doctor notes
        $listing->update([
            'doctor_notes' => $request->doctor_notes,
        ]);

        return back()->with('success', 'Notes updated successfully.');
    }

    /**
     * Generate PDF report for a radiology request.
     * Creates professional PDF with header, footer, and report content.
     */
    public function exportPdf($uuid)
    {
        // Load the request data
        $report = RequestListing::where('uuid', $uuid)->firstOrFail();

        // Render the report template to HTML
        $html = view('reports.sample-report', compact('report'))->render();

        // Initialize mPDF with custom margins
        $mpdf = new \Mpdf\Mpdf([
            'margin_top'    => 40,
            'margin_bottom' => 30,
        ]);

        // Set professional header with logo
        $mpdf->SetHTMLHeader('
            <div style="text-align: center; padding-bottom: 10px;border-bottom: 1px solid #666; margin-bottom:5px;">
                <div style="font-size: 12pt; font-weight: bold; margin-bottom: 20px">
                    [ NationalRad Sample Body Radiology Report ]
                </div>
                <img src="' . public_path('images/header-logo.png') . '" width="225" height="36" style="margin-bottom: 5px;">
            </div>
        ');

        // Set professional footer with company information
        $mpdf->SetHTMLFooter('
            <div style="text-align: center; font-size: 10pt; border-top: 1px solid #666; padding-top: 10px;">
                Report approved on<br>
                NationalRad | Headquartered: Florida | Diagnostic Imaging Services: Nationwide | 877.734.6674 | www.NationalRad.com
            </div>
        ');

        // Write HTML content to PDF
        $mpdf->WriteHTML($html);

        // Return PDF for download
        return response($mpdf->Output("report-$uuid.pdf", \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"report-$uuid.pdf\"");
    }
    
    /**
     * Generate doctor-specific PDF report.
     * Similar to exportPdf but uses different template for doctor view.
     */
    public function exportPdfDoctor($uuid)
    {
        // Load the request data
        $report = RequestListing::where('uuid', $uuid)->firstOrFail();

        // Render the doctor-specific report template
        $html = view('reports.sample-report-doctor', compact('report'))->render();

        // Initialize mPDF with custom margins
        $mpdf = new \Mpdf\Mpdf([
            'margin_top'    => 40,
            'margin_bottom' => 30,
        ]);

        // Set professional header with logo
        $mpdf->SetHTMLHeader('
            <div style="text-align: center; padding-bottom: 10px;border-bottom: 1px solid #666; margin-bottom:5px;">
                <div style="font-size: 12pt; font-weight: bold; margin-bottom: 20px">
                    [ NationalRad Sample Body Radiology Report ]
                </div>
                <img src="' . public_path('images/header-logo.png') . '" width="225" height="36" style="margin-bottom: 5px;">
            </div>
        ');

        // Set professional footer with company information
        $mpdf->SetHTMLFooter('
            <div style="text-align: center; font-size: 10pt; border-top: 1px solid #666; padding-top: 10px;">
                Report approved on<br>
                NationalRad | Headquartered: Florida | Diagnostic Imaging Services: Nationwide | 877.734.6674 | www.NationalRad.com
            </div>
        ');

        // Write HTML content to PDF
        $mpdf->WriteHTML($html);

        // Return PDF for download
        return response($mpdf->Output("report-doctor-$uuid.pdf", \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"report-doctor-$uuid.pdf\"");
    }

    // Commented out original preview method
    // public function previewReport($uuid)
    // {
    //     $report = RequestListing::where('uuid', $uuid)->firstOrFail();

    //     return view('reports.sample-report', compact('report'));
    // }

    /**
     * Preview PDF report in browser without downloading.
     * Generates PDF for inline viewing with professional formatting.
     */
    public function previewReport($uuid)
    {
        // Load the request data
        $report = RequestListing::where('uuid', $uuid)->firstOrFail();

        // Render the report template to HTML
        $html = view('reports.sample-report', compact('report'))->render();

        // Initialize mPDF with custom margins
        $mpdf = new \Mpdf\Mpdf([
            'margin_top'    => 40,
            'margin_bottom' => 30,
        ]);

        // Set header with logo
        $mpdf->SetHTMLHeader('
            <div style="text-align: center; padding-bottom: 10px;">
                <div style="font-size: 12pt; font-weight: bold; margin-bottom: 20px">
                    [ NationalRad Sample Body Radiology Report ]
                </div>
                <img src="' . storage_path('app/public/images/sure.png') . '" width="120" style="margin-bottom: 5px;">
            </div>
        ');

        // Set footer with company information
        $mpdf->SetHTMLFooter('
            <div style="text-align: center; font-size: 10pt; border-top: 1px solid #666; padding-top: 10px;">
                Report approved on<br>
                NationalRad | Headquartered: Florida | Diagnostic Imaging Services: Nationwide | 877.734.6674 | www.NationalRad.com
            </div>
        ');

        // Write HTML content to PDF
        $mpdf->WriteHTML($html);

        // Return PDF for inline viewing (not download)
        return response($mpdf->Output("report-$uuid.pdf", \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"report-$uuid.pdf\"");
    }

    /**
     * Update patient information for a request.
     * Handles date format conversion and validation.
     */
    public function updatePatient (Request $request, $uuid) {
        $authUser = auth()->user();
        $patient = RequestListing::where(["uuid" => $uuid])->first();
        
        // Check authorization
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin') && $patient->user_id !== $authUser->id) {
            abort(404);
        }

        // Convert date format from d/m/Y to Y-m-d for database storage
        if ($request->filled('patient_dob')) {
            try {
                $request->merge([
                    'patient_dob' => Carbon::createFromFormat('d/m/Y', $request->patient_dob)->format('Y-m-d')
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format.'], 422);
            }
        }

        // Validate patient information
        $validated = $request->validate([
            'patient_name' => 'required|string',
            'patient_dob' => 'required|string',
            'patient_phone' => 'required|string|min:10',
            'patient_email' => 'required|email',
            'patient_address' => 'required|string',
            'patient_postcode' => 'required|string',
        ]);

        // Update patient information
        $patient->update($validated);

        // Return updated data with formatted date
        return response()->json([
            'patient_name'     => $patient->patient_name,
            'patient_dob' => \Carbon\Carbon::parse($patient->patient_dob)->format('d/m/Y'),
            'patient_phone'    => $patient->patient_phone,
            'patient_email'    => $patient->patient_email,
            'patient_address'  => $patient->patient_address,
            'patient_postcode' => $patient->patient_postcode,
        ]);
    }

    /**
     * Update radiology request details (question and clinical history).
     * Allows admin, sub-admin, or request owner to update request information.
     */
    public function updateRadiology(Request $request, $uuid)
    {
        $authUser = auth()->user();
        $listing = RequestListing::where('uuid', $uuid)->firstOrFail();
        
        // Check authorization
        if (!$authUser->hasRole('admin') && !$authUser->hasRole('sub-admin') && $listing->user_id !== $authUser->id) {
            abort(404);
        }

        // Validate radiology request details
        $validated = $request->validate([
            'question'         => 'required|string',
            'clinical_history' => 'required|string',
            // Add 'modality' if you want to include it in the future
        ]);

        // Update radiology request information
        $listing->update($validated);

        // Return updated data
        return response()->json([
            'question'         => $listing->question,
            'clinical_history' => $listing->clinical_history,
            // 'modality'      => $listing->modality, // optional
        ]);
    }
}

