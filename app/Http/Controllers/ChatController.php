<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Chat;
use App\Models\RequestListing;
use Carbon\Carbon;

/**
 * ChatController handles all chat-related functionality for the radiology report management system.
 * This controller manages both regular user chats and admin-only chats for request listings.
 */
class ChatController extends Controller
{
    
    /**
     * Retrieve and format regular chat messages for a specific request listing.
     */
    public function getMessages($uuid)
    {
        // Find the request listing by UUID or return 404 if not found
        $listing = RequestListing::where('uuid', $uuid)->firstOrFail();

        // Retrieve all regular chat messages for this request listing with user relationship
        $messages = Chat::with('user')
            ->where('request_listing_id', $uuid)
            ->where('type', 'regular')
            ->orderBy('created_at')
            ->get();

        $currentUserId = auth()->id();
        //return response()->json($messages);

        // Transform messages into formatted HTML for frontend display
        $htmlMessages = $messages->map(function ($message) use ($currentUserId) {
            $user = $message->user;
            
            // Determine username: prefer dentist_name if available, otherwise use first_name + last_name
            $username = ucfirst( !empty($user->dentist_name)  ?  $user->dentist_name : ($user->first_name . ' ' . $user->last_name));
            //$username = $user->dentist_name ?? $user->first_name . ' ' . $user->last_name;
            
            // Get user avatar: use route for dynamic avatar or fallback to default image
            // $avatar = $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-doc-profile.jpg');
            $avatar = $user->avatar ? route('user.avatar', ['filename' => $user->avatar]) : asset('images/default-doc-profile.jpg');

            // Determine if message is from current user for styling purposes
            $isCurrentUser = ($user->id === $currentUserId);
            $classes = 'chat-message' . ($isCurrentUser ? ' current-user' : '');

            // Parse message timestamp for relative time display
            $messageTime = Carbon::parse($message->created_at);
            $now = Carbon::now();

            // Calculate relative time string for better user experience
            $diffInSeconds = $now->diffInSeconds($messageTime);
            $diffInMinutes = $now->diffInMinutes($messageTime);
            $diffInHours = $now->diffInHours($messageTime);

            /* Alternative time formatting logic (commented out):
            if ($diffInSeconds < 60) {
                $timeString = $diffInSeconds <= 5 ? 'a few seconds ago' : $diffInSeconds . ' seconds ago';
            } elseif ($diffInMinutes < 2) {
                $timeString = 'a minute ago';
            } elseif ($messageTime->isToday()) {
                // Format as: Today 9:30 AM
                $timeString = 'Today ' . $messageTime->format('g:i A');
            } elseif ($messageTime->isYesterday()) {
                $timeString = 'Yesterday ' . $messageTime->format('g:i A');
            } else {
                // Format with full date for older messages
                $timeString = $messageTime->format('M j, Y g:i A');
            }*/

            // Current time formatting logic
            if ($messageTime->isToday()) {
                // Format as: Today 9:30 AM
                $timeString = 'Today ' . $messageTime->format('g:i A');
            } elseif ($messageTime->isYesterday()) {
                $timeString = 'Yesterday ' . $messageTime->format('g:i A');
            } else {
                // Format with full date for older messages
                $timeString = $messageTime->format('M j, Y g:i A');
            }

            // Return formatted HTML for each message
            return '
            <div class="'.$classes.'">
                <img src="'.$avatar.'" alt="'.e($username).'" class="chat-avatar">
                <div class="chat-body">
                    <div class="chat-sender-container">
                    <div class="chat-sender">'.e($username).'</div>
                    <div>'.e($message->message).'</div>
                    </div>
                    <div class="chat-time" >'.$timeString.'</div>
                </div>
            </div>';
        });

        // Return JSON response with concatenated HTML messages
        return response()->json([
            'html' => $htmlMessages->implode(''),
        ]);
    }

    /**
     * Retrieve and format admin-only chat messages for a specific request listing.
     * Only accessible by users with admin or sub-admin roles.
     */
    public function getAdminMessages($uuid)
    {
        // Check if current user has admin privileges
        if (!auth()->user()->hasAnyRole(['admin', 'sub-admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Find the request listing by UUID or return 404 if not found
        $listing = RequestListing::where('uuid', $uuid)->firstOrFail();

        // Retrieve all admin chat messages for this request listing with user relationship
        $messages = Chat::with('user')
            ->where('request_listing_id', $uuid)
            ->where('type', 'admin')
            ->orderBy('created_at')
            ->get();

        $currentUserId = auth()->id();

        // Transform admin messages into formatted HTML for frontend display
        $htmlMessages = $messages->map(function ($message) use ($currentUserId) {
            $user = $message->user;
            
            // For admin messages, always use first_name + last_name format
            $username = $user->first_name . ' ' . $user->last_name;
            
            // Get user avatar: use route for dynamic avatar or fallback to default image
            // $avatar = $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-doc-profile.jpg');
            $avatar = $user->avatar ? route('user.avatar', ['filename' => $user->avatar]) : asset('images/default-doc-profile.jpg');

            // Determine if message is from current user for styling purposes
            $isCurrentUser = ($user->id === $currentUserId);
            $classes = 'chat-message' . ($isCurrentUser ? ' current-user' : '');

            // Parse message timestamp for relative time display
            $messageTime = Carbon::parse($message->created_at);
            $now = Carbon::now();

            // Format time string for display
            if ($messageTime->isToday()) {
                $timeString = 'Today ' . $messageTime->format('g:i A');
            } elseif ($messageTime->isYesterday()) {
                $timeString = 'Yesterday ' . $messageTime->format('g:i A');
            } else {
                $timeString = $messageTime->format('M j, Y g:i A');
            }

            // Return formatted HTML for each admin message
            return '
            <div class="'.$classes.'">
                <img src="'.$avatar.'" alt="'.e($username).'" class="chat-avatar">
                <div class="chat-body">
                    <div class="chat-sender-container">
                    <div class="chat-sender">'.e($username).'</div>
                    <div>'.e($message->message).'</div>
                    </div>
                    <div class="chat-time" >'.$timeString.'</div>
                </div>
            </div>';
        });

        // Return JSON response with concatenated HTML messages
        return response()->json([
            'html' => $htmlMessages->implode(''),
        ]);
    }

    /**
     * Send a new chat message for a specific request listing.
     * Validates message content and user permissions before saving.
     */
    public function sendMessage(Request $request, $uuid)
    {
        // Validate incoming request data
        $request->validate([
            'message' => 'required|string|max:1000', // Message is required, must be string, max 1000 chars
            'type' => 'required|in:regular,admin',   // Type must be either 'regular' or 'admin'
        ]);

        // Check admin permissions if trying to send admin message
        if ($request->type === 'admin' && !auth()->user()->hasAnyRole(['admin', 'sub-admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Find the request listing by UUID or return 404 if not found
        $listing = RequestListing::where('uuid', $uuid)->firstOrFail();

        // Create new chat message record
        Chat::create([
            'request_listing_id' => $uuid,           // Link to the request listing
            'user_id' => auth()->id(),               // Current authenticated user
            'message' => $request->message,          // The message content
            'type' => $request->type,                // Message type (regular or admin)
        ]);

        // Return success response
        return response()->json(['status' => 'sent']);
    }
}
