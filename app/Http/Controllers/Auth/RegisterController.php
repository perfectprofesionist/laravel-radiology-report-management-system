<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserMeta;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Mail\AdminNotificationMail;
use App\Mail\UserActivationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Models\City;
use App\Models\Country;
use Stripe\Charge;
use Stripe\Stripe;

use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\PaymentMethod;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {

        $stripeKey = env('STRIPE_SECRET');
        Stripe::setApiKey($stripeKey);

        // Create a temporary customer for SetupIntent
        // $customer = Customer::create();

        $intent = SetupIntent::create(); // creates a SetupIntent for saving card

        return view('auth.register', [
            'clientSecret' => $intent->client_secret,
            // 'tempCustomerId' => $customer->id,
        ]);
        // return view('auth.register');
    }

    public function register(Request $request)
    {
           
        // dd($request->all());
        $this->validator($request->all())->validate();
        $password = !empty($request->password) ? Hash::make($request->password) : null;

        $uuid = (string) Str::uuid();

        $user = User::create([
            'uuid' => $uuid,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $password,
        ]);

        // Assign 'user' role to the newly created user
        $role = Role::where('name', 'user')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        // Set meta data using the Metable trait
        $metaFields = [
            'mobile_number' => $request->mobile_number,
            'dentist_name' => $request->dentist_name,
            'practice_name' => $request->practice_name,
            'practice_address' => $request->practice_address,
            'city' => $request->city,
            'state' => $request->state,
            'post_code' => $request->post_code,
            // 'gdc_number' => $request->gdc_number,
            'routine_phone' => $request->routine_phone,
            'urgent_phone' => $request->urgent_phone,
        ];

        foreach ($metaFields as $key => $value) {
            $user->setMeta($key, $value);
        }

        // Save the meta data
        // $user->save();
        // if ($request->filled('payment_method')) {

        $stripeKey = env('STRIPE_SECRET');
        Stripe::setApiKey($stripeKey);

        // Create a new Stripe Customer with name/email
        $customer = \Stripe\Customer::create([
            'name' => $user->username,
            'email' => $user->email,
            'metadata' => [
                'uuid'    => $user->uuid,
            ],
        ]);

            // Attach payment method to customer
            // PaymentMethod::attach(
            //     $request->payment_method,
            //     ['customer' => $customer->id]
            // );

            // $paymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method);
            // $paymentMethod->attach(['customer' => $customer->id]);
            
            // Set default payment method for future charges
            // \Stripe\Customer::update($customer->id, [
            //     'invoice_settings' => [
            //         'default_payment_method' => $request->payment_method,
            //     ],
            // ]);
            
            // $user->setMeta('stripe_customer_id', $customer->id);

            // dd($user->stripe_customer_id);
        $user->stripe_customer_id = $customer->id;
            // $user->save();
        // }


        // Save customer_id to your user model for future payments
        $user->save();

        $adminUser = User::whereHas('roles', function ($q) {
            $q->where('username', 'admin');
        })->get();


        // mail to the adminuser who has the role admin 
        foreach ($adminUser as $admin) {
            Mail::to($admin->email)->send(new AdminNotificationMail($user));
        }

        // Notify admin
        foreach ($adminUser as $admin) {
            $admin->notify(new NewUserRegistered($user));
        }


        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'User and payment method saved successfully.',
        //     // 'user' => $user,
        //     // 'stripe_customer_id' => $customer->id,
        //     // 'payment_method' => $request->payment_method,
        // ]);
        return redirect()->route('thank.you');
        // return redirect()->route('register')->with('success', "Thanks for registering! Your account is under review — we'll email you once i's approved.");
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => [
                'required',
                'string',
                'min:4',
                'max:20',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username',
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],

            'dentist_name' => ['required', 'string', 'max:255'],
            // 'gdc_number' => ['required', 'string', 'regex:/^[A-Za-z0-9]{8}$/'],
            'practice_name' => ['required', 'string', 'max:255'],
            'practice_address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'post_code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s\-]+$/'],
            'mobile_number' => ['required', 'string', 'min:9', 'max:15'],
            'routine_phone' => ['required', 'string', 'min:9', 'max:15'],
            'urgent_phone' => ['required', 'string', 'min:9', 'max:15'],
            'confirm_ahpra' => ['accepted'],
            'agree_terms' => ['accepted'],
            'payment_method' => ['nullable'],

        ], [
            'username.required' => 'Username is required.',
            'username.string' => 'Username must be a string.',
            'username.min' => 'Username must be at least 4 characters.',
            'username.max' => 'Username must not be more than 20 characters.',
            'username.regex' => 'Username can only contain letters ( A-Z , a-z , 0-9 , _ ).',
            'username.unique' => 'This username is already taken.',

            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'email.unique' => 'This email is already registered.',

            // 'gdc_number.required' => 'GDC number is required.',
            // 'gdc_number.string' => 'GDC number must be a string.',
            // 'gdc_number.regex' => 'GDC number must be 8 characters long and alphanumeric.',

            'dentist_name.required' => 'Dentist name is required.',
            'practice_name.required' => 'Practice name is required.',
            'practice_address.required' => 'Practice address is required.',
            
            'city.required' => 'City/Suburb is required.',
            'city.string' => 'City/Suburb must be a string.',
            'city.max' => 'City/Suburb must not exceed 255 characters.',

            'state.required' => 'State is required.',
            'state.string' => 'State must be a string.',
            'state.max' => 'State must not exceed 255 characters.',

            'post_code.required' => 'Postcode is required.',
            'post_code.string' => 'Postcode must be a string.',
            'post_code.max' => 'Postcode must not exceed 20 characters.',
            'post_code.regex' => 'Postcode may only contain letters, numbers, spaces, or hyphens.',
            
            'mobile_number.required' => 'Mobile phone number is required.',
            'mobile_number.min' => 'Mobile phone number must be at least 9 digits.',
            'mobile_number.max' => 'Mobile phone number must not exceed 15 digits.',

            'routine_phone.required' => 'Routine phone number is required.',
            'routine_phone.min' => 'Routine phone number must be at least 9 digits.',
            'routine_phone.max' => 'Routine phone number must not exceed 15 digits.',
            'urgent_phone.required' => 'Urgent phone number is required.',
            'urgent_phone.min' => 'Urgent phone number must be at least 9 digits.',
            'urgent_phone.max' => 'Urgent phone number must not exceed 15 digits.',

            'confirm_ahpra.accepted' => 'You must confirm you are a registered dental professional.',
            'agree_terms.accepted' => 'You must agree to the Terms & Conditions and Privacy Policy.',

        ]);
    }

    public function suggestUsernames(Request $request)
    {
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', substr($request->input('username'), 0, 20));

        if (!User::where('username', $username)->exists()) {
            return response()->json([
                'exists' => false,
                'suggestions' => [],
                'message' => ''
            ]);
        }

        $base = substr($username, 0, 10);
        $suggestions = [];

        while (count($suggestions) < 4) {
            $options = [
                fn($b) => $b . '_' . date('d') . rand(1, 9),
                fn($b) => $b . rand(100, 999),
                fn($b) => $b . '_' . rand(10, 99),
                fn($b) => $b . '_' . str_repeat((string)rand(1, 9), rand(2, 3)),
            ];

            foreach ($options as $option) {
                $suggestion = $option($base);
                if (!User::where('username', $suggestion)->exists() && !in_array($suggestion, $suggestions)) {
                    $suggestions[] = $suggestion;
                    if (count($suggestions) === 4) break;
                }
            }
        }

        return response()->json([
            'exists' => true,
            'suggestions' => $suggestions,
            'message' => 'Username already exists. Please choose a different one.'
        ]);
    }


    public function activateUser($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        if (!auth()->check()) {
            return redirect()->route('login');
        }
        // Check if the authenticated user is an admin
        if (!auth()->user()->hasRole('admin')) {
            abort(404);
        }

        // Check if the user is already active
        if ($user->is_active) {
            return redirect()->route('users.index')->with('success', "The account is activated now!");
        }

        // Set the user as active
        $user->update(['is_active' => true]);

        // Send activation email to the user
        Mail::to($user->email)->send(new UserActivationMail($user));

        // Redirect to the 'thank.you' page after activation with a personalized success message
        // return redirect()->route('thank.you')->with('message', "The account for username '{$user->username}' has been successfully activated!");
       return redirect()->route('users.index')->with('success', "The account is activated now!");
    }

    
    public function handleAccountAccess($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        if (is_null($user->password)) {
            return redirect()->route('password.create', $user->uuid);
        }

        return redirect()->route('login')->with('message', 'You already have a password. Please log in.');
    }

    // Show the create password form
    public function showCreatePasswordForm($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        return view('auth.create-password', compact('user'));
    }

    // Store the new password
    public function storePassword(Request $request, $uuid)
    {
        // Validate the password
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        // Fetch the user by ID and update the password
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('message', 'Password set successfully. You can now log in.');
    }

}
