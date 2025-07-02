<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use App\Models\UserMeta;
use Carbon\Carbon;

/**
 * UserController handles user management operations including CRUD operations and role-based functionality.
 * Provides DataTables integration for user listing and supports different user types with meta fields.
 */
class UserController extends Controller
{

    /**
     * Display list of users with DataTables integration and search functionality.
     * Handles AJAX requests for dynamic data loading and filtering.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            // Build query with roles relationship
            $users = User::with('roles');

            // Apply custom search filter if provided
            if ($search = $request->input('search_custom')) {
                $users->where(function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('roles', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Return DataTables response with custom columns
            return DataTables::of($users->get())
                ->addIndexColumn()
                ->addColumn('dentist_name', function ($user) {
                    return $user->getMeta('dentist_name');
                })
                ->addColumn('practice_name', function ($user) {
                    return $user->getMeta('practice_name');
                })
                ->addColumn('mobile_number', function ($user) {
                    return $user->getMeta('mobile_number');
                })
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->implode(', ');
                })
                ->addColumn('avatar', function($user) {
                    return $user->avatar; // Return avatar filename
                })
                ->addColumn('action', function ($row) {
                    // Create edit button
                    $edit = '<a href="' . route('users.edit', [$row->id]) . '" class="edit btn btn-info btn-sm">Edit</a>';
                    
                    // Create activate button for inactive users
                    if (!$row->is_active) {
                        $view = '<a href="' . route('check.user-details', [$row->uuid]) . '" class="edit btn btn-info btn-sm">Activate</a>';
                    } else {
                        $view = '';
                    }
                    
                    // Create delete button with JavaScript confirmation
                    $delete = '<button data-id="' . $row->id . '" data-username="' . e($row->username) . '" class="btn btn-danger btn-sm delete-user">Delete</button>';

                    return $view . ' ' . $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new user.
     * Loads available roles for user assignment.
     */
    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in the database.
     * Handles role-based validation and meta field storage.
     */
    public function store(Request $request)
    {
        // Base validation rules for all users
        $baseRules = [
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        // Add first and last name validation for non-user roles (admin, sub-admin)
        if ($request->role !== 'user') {
            $baseRules['first_name'] = 'required|string|max:255';
            $baseRules['last_name'] = 'required|string|max:255';
        }

        // Add user-specific validation rules for 'user' role (dentists)
        if ($request->role === 'user') {
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

        // Create the user with basic information
        $user = User::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'is_active' => true,
        ]);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Storage::delete('private_avatars/' . $user->avatar);
            }
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('private_avatars', $filename);
            $user->avatar = $filename;
            $user->save();
        }

        // Assign the selected role to the user
        $user->assignRole($validated['role']);

        // Store meta fields based on user role
        if ($request->role !== 'user') {
            // Store first and last name for admin/sub-admin users
            $user->setMeta('first_name', $request->first_name);
            $user->setMeta('last_name', $request->last_name);
            $user->save();
        }

        if ($request->role === 'user') {
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

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    // Commented out alternative implementation of store method
    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $validated = $request->validate([
    //         'username' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:users,email,',
    //         'password' => 'required|string|min:8|confirmed',
    //         'role' => 'required|exists:roles,name',
    //         'dentist_name' => 'required|string|max:255',
    //         'practice_name' => 'required|string|max:255',
    //         'practice_address' => 'required|string|max:255',
    //         'city' => 'required|string|max:255',
    //         'state' => 'required|string|max:255',
    //         'mobile_number' => 'required|digits_between:9,15',
    //         'gdc_number' => 'required|string',
    //         'home_address' => 'required|string',
    //         'home_post_code' => 'required|string',
    //         'hospital_name' => 'required|string',
    //         'hospital_address' => 'required|string',
    //         'hospital_post_code' => 'required|string',
    //         'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
    //     ]);

    //     $user = User::create([
    //         'uuid' => (string) \Illuminate\Support\Str::uuid(),
    //         'username' => $validated['username'],
    //         'email' => $validated['email'],
    //         'password' => bcrypt($validated['password']),
    //         'is_active' => true
    //     ]);

    //     if ($request->hasFile('avatar')) {
    //         // Delete old avatar if exists
    //         if ($user->avatar) {
    //             \Storage::delete('private_avatars/' . $user->avatar);
    //         }
    //         $avatar = $request->file('avatar');
    //         $filename = time() . '.' . $avatar->getClientOriginalExtension();
    //         $avatar->storeAs('private_avatars', $filename); // stored in storage/app/private_avatars
    //         $user->avatar = $filename;
    //         $user->save();
    //     }

    //     $user->assignRole($validated['role']);

    //       $metaFields = [
    //         'dentist_name' => $request->dentist_name,
    //         'practice_name' => $request->practice_name,
    //         'practice_address' => $request->practice_address,
    //         'city' => $request->city,
    //         'state' => $request->state,
    //         'mobile_number' => $request->mobile_number,
    //         'gdc_number' => $request->gdc_number,
    //         'home_address' => $request->home_address,
    //         'home_post_code' => $request->home_post_code,
    //         'hospital_name' => $request->hospital_name,
    //         'hospital_address' => $request->hospital_address,
    //         'hospital_post_code' => $request->hospital_post_code,
    //     ];

    //     foreach ($metaFields as $key => $value) {
    //         if (!is_null($value)) {
    //             $user->setMeta($key, $value);
    //         }
    //     }

    //     $user->save();

    //     return redirect()->route('users.index')->with('success', 'User created successfully!');
    // }

    /**
     * Display the specified user resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified user.
     * Loads user data with roles for editing.
     */
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     * Handles role-based validation and meta field updates.
     */
    public function userupdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        Log::info("Incoming request data:", $request->all());
        
        // Base validation rules for all users
        $baseRules = [
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        // Add first and last name validation for non-user roles
        if ($request->role !== 'user') {
            $baseRules['first_name'] = 'required|string|max:255';
            $baseRules['last_name'] = 'required|string|max:255';
        }

        // Add user-specific validation rules for 'user' role
        if ($request->role === 'user') {
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
        Log::info("User basic fields updated");

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

        // Update user role
        $user->syncRoles([$validated['role']]);

        // Store meta fields based on user role
        if ($request->role !== 'user') {
            // Store first and last name for admin/sub-admin users
            $user->setMeta('first_name', $request->first_name);
            $user->setMeta('last_name', $request->last_name);
            $user->save();
        }

        if ($request->role === 'user') {
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

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     * Permanently deletes the user and all associated data.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
