<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CommonController handles shared functionality and utility methods used across the application.
 * This controller provides common operations like location data retrieval and file serving.
 */
class CommonController extends Controller
{
    
    /**
     * Retrieve all cities in the United Kingdom for dropdown/selection purposes.
     * Returns cities with their associated state information.
     */
    public function getCities()
    {
        // Find the United Kingdom country record
        $country = Country::where('name', 'United Kingdom')->first();

        // Get all cities that belong to states in the United Kingdom
        // Include state information for each city
        $cities = City::whereHas('state', function ($query) use ($country) {
            $query->where('country_id', $country->id);
        })
        ->with('state:id,name')  // Eager load state data (only id and name fields)
        ->orderBy('name')        // Sort cities alphabetically by name
        ->get(['id', 'name', 'state_id']);  // Select only needed fields

        return response()->json($cities);
    }

    // Commented out method for getting states by city - alternative approach
    // public function getStatesByCity(Request $request)
    // {
    //     $cityId = $request->input('city_id');
    //     $city = DB::table('cities')->where('id', $cityId)->first();

    //     if (!$city) {
    //         return response()->json(['error' => 'City not found'], 404);
    //     }

    //     $state = DB::table('states')->where('id', $city->state_id)->first();

    //     return response()->json($state);
    // }

    /**
     * Display the registration thank you page with optional request status parameter.
     * Used to show different messages based on the request status.
     */
     public function show(Request $request)
    {
        // Get the request status from query parameter (e.g., ?isrequest=RequestSubmitted)
        $isrequest = $request->query('isrequest');
        
        // Return the thank you page view with the request status
        return view('auth.register-thanks', compact('isrequest'));
    }

    /**
     * Serve private files stored in the application's private storage directory.
     * Includes security checks to ensure only authenticated users can access files.
     */
    public function getPrivateFiles($filename) {
        // Sanitize the filename to prevent directory traversal attacks
        $filename = basename($filename);
        
        // Construct the full path to the private file
        $path = storage_path('app/public/private/uploads/' . $filename);
        
        // Check if the file exists, return 404 if not found
        if (!file_exists($path)) {
            abort(404);
        }

        // Ensure user is authenticated before serving the file
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Serve the file as a response
        return response()->file($path);
    }

    /**
     * Serve user avatar images stored in the private avatars directory.
     * Currently has commented out authorization checks for development/testing.
     */
    public function serveAvatar($filename)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Authorization check is currently commented out for development
        // This would normally ensure only admins or the avatar owner can access the file
    //    if (
    //         !$user ||
    //         (!($user->hasRole('admin') || $user->hasRole('subadmin')) && $user->avatar !== $filename)
    //     ) {
    //         abort(403);
    //     }

        // Construct the full path to the avatar file
        $path = storage_path('app/private/private_avatars/' . $filename);

        // Check if the avatar file exists, return 404 if not found
        if (!\File::exists($path)) {
            abort(404);
        }

        // Serve the avatar file as a response
        return response()->file($path);
    }

}
