<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/scan-upload-page';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Override the default login username to 'login' (can be email or username).
     *
     * @return string
     */
    public function username()
    {
        return 'username_email';
    }

    /**
     * Validate the user login request.
     * We override to validate the 'login' field and 'password'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username_email' => 'required|string',
            'password' => 'required|string',
        ], [
            'username_email.required' => 'The username or email field is required.',
            'password.required' => 'The password field is required.',
        ]);
    }


    /**
     * Attempt to log the user into the application.
     * Here we check if 'login' input is email or username and attempt accordingly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $login = $request->input('username_email');
        $password = $request->input('password');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = \App\Models\User::where($field, $login)->first();

        if ($user) {
            // Check if password matches (using Hash check)
            if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                // Check if user is active
                if ($user->is_active != 1) {
                    // User inactive - do NOT login, just return false to prevent login
                    return false;
                }
            }
    }

        return Auth::attempt([
            $field => $login,
            'password' => $password,
            'is_active' => 1,
        ], $request->filled('remember'));
    }

    /**
     * Get the needed authentication credentials from the request.
     * (Optional override, since we're handling it in attemptLogin, this can be omitted)
     */
    // protected function credentials(Request $request)
    // {
    //     $login = $request->input('login');
    //     $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    //     return [
    //         $field => $login,
    //         'password' => $request->input('password'),
    //     ];
    // }



      protected function authenticated(Request $request, $user)
    {
        if ($user->hasAnyRole(['admin', 'sub-admin'])) {
            return redirect()->route('request-listing.index');  // admin and sub-admin route
        }

        return redirect()->route('request-listing.indexuser'); // user route
    }
    

    
    /**
     * Customize the failed login response to show errors on 'login' field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
         $login = $request->input('username_email');
    $password = $request->input('password');
    $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $user = \App\Models\User::where($field, $login)->first();

    if ($user) {
        // Check password matches
        if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            // Password matches but user is inactive
            if ($user->is_active != 1) {
                throw ValidationException::withMessages([
                    'username_email' => ['Your account is not active. Please contact support.'],
                ]);
            }
        }
    }

    // Default failed login response
    throw ValidationException::withMessages([
        'username_email' => [trans('auth.failed')],
    ]);
    }
}
