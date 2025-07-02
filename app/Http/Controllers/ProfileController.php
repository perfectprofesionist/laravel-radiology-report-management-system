<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserMeta;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

/**
 * ProfileController handles user profile management, payment card operations, and account settings.
 * Integrates with Stripe for payment method management and provides role-based profile updates.
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile page with role information.
     * Shows profile data and available roles for the authenticated user.
     */
     public function show()
    {
        // Get the currently authenticated user and all available roles
        $user = auth()->user();
        $roles = Role::all();

        return view('profile.show', compact('user', 'roles'));
    }

    /**
     * Display user's saved payment cards from Stripe.
     * Creates Stripe customer if not exists and retrieves payment methods.
     */
     public function cards()
    {
        $user = auth()->user();

        // Check if user has Stripe customer ID, return empty cards if not
        if (!$user->stripe_customer_id) {
            return view('profile.cards', compact('user'))->with('cards', []);
        }

        // Initialize Stripe with secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve all card payment methods for the customer
        $cards = PaymentMethod::all([
            'customer' => $user->stripe_customer_id,
            'type' => 'card',
        ]);

        return view('profile.cards', [
            'user' => $user,
            'cards' => $cards->data,
        ]);
    }

    /**
     * Delete a payment card from Stripe.
     * Verifies card ownership before deletion.
     */
    public function deleteCard($id)
    {
        $user = auth()->user();

        // Initialize Stripe with secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve payment method from Stripe
            $paymentMethod = PaymentMethod::retrieve($id);
            
            // Verify card belongs to the authenticated user
            if ($paymentMethod->customer === $user->stripe_customer_id) {
                $paymentMethod->detach(); // Remove card from customer
                return back()->with('success', 'Card deleted successfully.');
            } else {
                return back()->withErrors('You are not authorized to delete this card.');
            }
        } catch (\Exception $e) {
            return back()->withErrors('Error deleting card: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for adding a new payment card.
     * Passes Stripe publishable key for frontend integration.
     */
    public function showAddCardForm()
    {
        // Pass the Stripe publishable key for frontend JavaScript integration
        return view('profile.add-card', [
            'stripeKey' => env('STRIPE_KEY'),
        ]);
    }

    /**
     * Store a new payment card in Stripe.
     * Creates Stripe customer if needed and attaches payment method.
     */
    public function storeCard(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Create Stripe customer if user doesn't have one
        if (!$user->stripe_customer_id) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name'  => $user->username,
                'metadata' => [
                    'uuid' => $user->uuid,
                ],
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Validate payment method ID
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Attach payment method to customer
            \Stripe\PaymentMethod::retrieve($request->payment_method)->attach([
                'customer' => $user->stripe_customer_id,
            ]);

            // Set as default payment method
            \Stripe\Customer::update($user->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Card added successfully.',
                'redirect' => route('cards') // optional
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add card: ' . $e->getMessage()], 400);
        }
    }

    // Commented out alternative implementation of storeCard method
    // public function storeCard(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!$user->stripe_customer_id) {
    //         // Create Stripe customer if not exists
    //         Stripe::setApiKey(env('STRIPE_SECRET'));
    //         $customer = \Stripe\Customer::create([
    //             'email' => $user->email,
    //             'name'  => $user->name,
    //             'metadata' => [
    //                 'uuid'    => $user->uuid,
    //             ],
    //         ]);
    //         $user->stripe_customer_id = $customer->id;
    //         $user->save();
    //     }

    //     $request->validate([
    //         'payment_method' => 'required|string',
    //     ]);
    //     // dd($request);
    //     try {
    //         Stripe::setApiKey(env('STRIPE_SECRET'));

    //         // Attach payment method
            
    //         $response = \Stripe\PaymentMethod::retrieve($request->payment_method)->attach([
    //             'customer' => $user->stripe_customer_id,
    //         ]);
    //         // return $response;

    //         // Optionally set as default payment method
    //         \Stripe\Customer::update($user->stripe_customer_id, [
    //             'invoice_settings' => [
    //                 'default_payment_method' => $request->payment_method,
    //             ],
    //         ]);

    //         return redirect()->route('cards')->with('success', 'Card added successfully.');
    //     } catch (\Exception $e) {
    //        // dd($e->getMessage());
    //        session()->flash('test', 'yes');
    //         return back()->withErrors(['card_error' => 'Failed to add card: ' . $e->getMessage()]);
    //     }
    // }

    /**
     * Update user profile information with role-based validation.
     * Handles different validation rules for different user roles.
     */
    public function profileupdate(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        // Commented out original validation rules
        // $validated = $request->validate([
        //     'gdc_number' => 'required|string|max:255',
        //     'mobile_number' => 'required|digits_between:9,15',
        //     'insurance_expired_date' => 'required|date_format:d/m/Y',
        //     'next_appraisal_date' => 'required|date_format:d/m/Y',
        //     'home_address' => 'required|string|max:255',
        //     'home_post_code' => 'required|string|max:255',
        //     'hospital_name' => 'required|string|max:255',
        //     'hospital_address' => 'required|string|max:255',
        //     'hospital_post_code' => 'required|string|max:255',
        //     'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        // ]);
        
        // Base validation rules for all users
        $baseRules = [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            // 'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        // Add first and last name validation for non-user roles (admin, sub-admin)
        if ($role !== 'user') {
            $baseRules['first_name'] = 'required|string|max:255';
            $baseRules['last_name'] = 'required|string|max:255';
        }

        // Add user-specific validation rules for 'user' role
        if ($role === 'user') {
            $baseRules = array_merge($baseRules, [
                'dentist_name' => 'required|string|max:255',
                'practice_name' => 'required|string|max:255',
                'practice_address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'mobile_number' => 'required|digits_between:9,15',
                'gdc_number' => 'required|string|max:255',
                'home_address' => 'required|string|max:255',
                'home_post_code' => 'required|string|max:255',
                'hospital_name' => 'required|string|max:255',
                'hospital_address' => 'required|string|max:255',
                'hospital_post_code' => 'required|string|max:255',
            ]);
        }

        $validated = $request->validate($baseRules);

        Log::info("Validation passed");

        // Update basic user fields
        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);
        //  Log::info("User basic fields updated");

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Storage::delete('private_avatars/' . $user->avatar);
            }
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('private_avatars', $filename); // stored in storage/app/private_avatars
            $user->avatar = $filename;
            $user->save();
        }

        // Commented out role update functionality
        // Update role
        // $user->syncRoles([$validated['role']]);

        // Commented out date conversion and meta fields handling
        // // Convert dates from dd/mm/yyyy to Y-m-d format for storage
        // $metaFields = [
        //     'gdc_number' => $request->gdc_number,
        //     'mobile_number' => $request->mobile_number,
        //     'insurance_expired_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->insurance_expired_date)->format('Y-m-d'),
        //     'next_appraisal_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->next_appraisal_date)->format('Y-m-d'),
        //     'home_address' => $request->home_address,
        //     'home_post_code' => $request->home_post_code,
        //     'hospital_name' => $request->hospital_name,
        //     'hospital_address' => $request->hospital_address,
        //     'hospital_post_code' => $request->hospital_post_code,
        // ];

        // foreach ($metaFields as $key => $value) {
        //     if (!is_null($value)) {
        //         $user->setMeta($key, $value);
        //     }
        // }

        // Store meta fields based on user role
        if ($role !== 'user') {
            // Store first and last name for admin/sub-admin users
            $user->setMeta('first_name', $request->first_name);
            $user->setMeta('last_name', $request->last_name);
            $user->save();
        }

        if ($role == 'user') {
            // Store comprehensive meta fields for regular users (dentists)
            $metaFields = [
                'dentist_name' => $request->dentist_name,
                'practice_name' => $request->practice_name,
                'practice_address' => $request->practice_address,
                'city' => $request->city,
                'state' => $request->state,
                'mobile_number' => $request->mobile_number,
                'gdc_number' => $request->gdc_number,
                'home_address' => $request->home_address,
                'home_post_code' => $request->home_post_code,
                'hospital_name' => $request->hospital_name,
                'hospital_address' => $request->hospital_address,
                'hospital_post_code' => $request->hospital_post_code,
            ];

            // Store each meta field if value is not null
            foreach ($metaFields as $key => $value) {
                if (!is_null($value)) {
                    $user->setMeta($key, $value);
                }
            }
            $user->save();
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the password change form for the authenticated user.
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user(); // Get the currently authenticated user

        return view('profile.change-password', compact('user'));
    }

    /**
     * Update user password with strong validation requirements.
     * Validates current password and enforces strong password policy.
     */
    public function updatePassword(Request $request)
    {
        // Validate password change request with strong password requirements
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:32',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,32}$/', // Strong password regex
                'confirmed', // matches new_password_confirmation
            ],
        ]);

        $user = Auth::user();

        // Verify current password before allowing change
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password with new hashed password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('password.change.form')->with('success', 'Password updated successfully!');
    }
}
