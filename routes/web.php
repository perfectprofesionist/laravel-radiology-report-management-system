<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RequestListingController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;


// Laravel route definitions for the Radiology Request Portal
// Handles authentication, user roles, file uploads, notifications, and more

// Main landing route: Redirects users based on authentication and role
Route::get('/', function () {
    if (!Auth::check()) { // If not logged in, redirect to login
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Redirect admin/sub-admin to admin dashboard, users to user dashboard
    if ($user->hasAnyRole(['admin', 'sub-admin'])) {
        return redirect()->route('request-listing.index');
    }

    return redirect()->route('request-listing.indexuser');
});

// Authentication routes (login, register, etc.)
Auth::routes();

// Export and PDF generation routes for request listings
Route::get('/request-listing/export/{uuid}', [RequestListingController::class, 'exportDoc'])->name('request-listing.export');
Route::get('/request-listing/pdf/{uuid}', [RequestListingController::class, 'exportPdf'])->name('request-listing.pdf');
Route::get('/request-listing/pdfdoctor/{uuid}', [RequestListingController::class, 'exportPdfDoctor'])->name('request-listing.pdfdoctor');
Route::get('/preview-report/{uuid}', [RequestListingController::class, 'previewReport'])->name('previewReport');

// Group routes that require authentication
Route::middleware(['auth'])->group(function () {
    // User activation and details check
    Route::get('/check_details_to_activate/{uuid}', [HomeController::class, 'checkUserDetails'])->name('check.user-details');
    Route::get('/admin/activate/{uuid}', [RegisterController::class, 'activateUser'])->name('admin.activate_user');

    // Modalities management (CRUD)
    Route::get('/modalities', [ModalityController::class, 'index'])->name('modalities.index');
    Route::resource('modalities', ModalityController::class)->except('index');

    // Scan upload and management
    Route::get('/scan-upload-page', [ScanController::class, 'scanupload'])->name('scan.upload');
    Route::get('/scan-upload-page/{uuid}', [ScanController::class, 'scanuploadedit'])->name('scan.upload.page'); 
    Route::put('/scan-upload-page/{uuid}', [ScanController::class, 'update'])->name('scan.update');
    Route::post('/file/post', [ScanController::class, 'uploadform'])->name('scan.uploadform');
    Route::post('/file/update/{uuid}', [ScanController::class, 'uploadformupdate'])->name('scan.formupdate');
    Route::post('/stripe/charge', [ScanController::class, 'ajaxCharge'])->name('stripe.ajax.charge');
    Route::post('/upload-advanced', [ScanController::class, 'upload']);
    Route::post('/remove-uploaded-file', [ScanController::class, 'removeUploadedFileScan'])->name('remove.uploaded.file');

    // Admin and sub-admin only: request listing dashboard
    Route::middleware(['auth', 'role:admin|sub-admin'])->group(function () {
        Route::get('/request-listing', [RequestListingController::class, 'index'])->name('request-listing.index');
    });

    // Regular users only: user dashboard
    Route::middleware(['auth', 'role:user'])->group(function () {
        Route::get('/request-listing-user', [RequestListingController::class, 'indexuser'])->name('request-listing.indexuser');
    });

    // View and chat for request listings
    Route::get('/request-listing/{uuid}', [RequestListingController::class, 'view'])->name('request-listing.view');
    Route::get('/request-listing/{uuid}/chat', [RequestListingController::class, 'chat'])->name('request-listing.chat');

    // Chat message APIs
    Route::get('/chat/{uuid}/messages', [ChatController::class, 'getMessages']);
    Route::get('/chat/{uuid}/admin-messages', [ChatController::class, 'getAdminMessages']);
    Route::post('/chat/{uuid}/send', [ChatController::class, 'sendMessage']);

    // Update status and notes for request listings
    Route::post('/request-listing/{uuid}/update-status-notes', [RequestListingController::class, 'updateStatusNotes'])->name('request-listing.update-status-notes');
    Route::post('/request-listing/{uuid}/update-status', [RequestListingController::class, 'updateStatus'])->name('request-listing.update-status');

    // Secure file access
    Route::get('/files/{filename}', [CommonController::class, "getPrivateFiles"])->name('scan.file');

    // Profile management (view, update, change password)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'profileupdate'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.change.form');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('password.update.custom');

    // Card management (CRUD)
    Route::get('/cards', [ProfileController::class, 'cards'])->name('cards');
    Route::delete('/cards/{id}', [ProfileController::class, 'deleteCard'])->name('cards.delete');
    Route::get('/cards/add', [ProfileController::class, 'showAddCardForm'])->name('cards.add');
    Route::post('/cards/store', [ProfileController::class, 'storeCard'])->name('cards.store');

    // File uploads and downloads for scans
    Route::post('/upload-files', [FileController::class, 'uploadfiles']);
    Route::post('/remove-file', [FileController::class, 'removeUploadedFile'])->name('removeUploadedFile');
    Route::get('/scan-files/{filename}', [FileController::class, 'downloadFile']); // Download scan file
    Route::post('/upload-files/{type}', [FileController::class, 'uploadFilesWithType'])->name('uploadFilesWithType');
    Route::get('show-files/{request_uuid}/{type}', [FileController::class, 'showFilesWithType'])->name('showFilesWithType');

    // Patient and radiology info update
    Route::put('/patient/update/{uuid}', [RequestListingController::class, 'updatePatient'])->name('patient.update');
    Route::post('/request-listing/{uuid}/update-doctor-notes', [RequestListingController::class, 'updateStatusDoctorNotes'])->name('request-listing.update-doctor-notes');   
    Route::put('/radiology/update/{uuid}', [RequestListingController::class, 'updateRadiology'])->name('radiology.update');

    // User update (admin)
    Route::post('/user/update/{id}', [UserController::class, 'userupdate'])->name('userupdate');

    // Notification redirect and unread notifications API
    Route::get('/notifications/redirect', [NotificationController::class, 'redirectNotification'])->name('notifications.redirect');
    Route::get('/notifications/unread', function () {
        // Returns unread notifications for the authenticated user
        return auth()->user()->notifications()->whereNull('read_at')->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'icon' => $notification->data['icon'] ?? asset('images/yellow-notification-icon.png'),
                'url' => route('notifications.redirect', ['notification' => $notification->id]),
                'class' => $notification->data['class'] ?? '',
            ];
        });
    })->name('notifications.unread');

    // Stripe payment setup intent
    Route::post('/stripe/setup-intent', [ScanController::class, 'createSetupIntent'])->name('stripe.setup.intent');

    // Serve user avatar images
    Route::get('/avatar/{filename}', [CommonController::class, 'serveAvatar'])->name('user.avatar');

});

// Admin-only user management (CRUD)
Route::middleware(['auth','role:admin'])->group(function () { 
    Route::resource('users', UserController::class);
});

// AJAX: Get cities for dropdowns
Route::get('/get-cities', [CommonController::class, 'getCities'])->name('ajax.get.cities');

// Registration and username suggestion routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/username-suggestions', [RegisterController::class, 'suggestUsernames'])->name('username.suggestions');

// Thank you page after registration
Route::get('thank-you', [CommonController::class, 'show'])->name('thank.you');

// Account access and password creation routes
Route::get('/account-access/{uuid}', [RegisterController::class, 'handleAccountAccess'])->name('account.access');
Route::get('/create-password/{uuid}', [RegisterController::class, 'showCreatePasswordForm'])->name('password.create');
Route::post('/store-password/{uuid}', [RegisterController::class, 'storePassword'])->name('password.store');

// Status management: approve status change for request listings
Route::post('/request-listing/{uuid}/approve-status', [RequestListingController::class, 'approveStatusChange'])
    ->name('request-listing.approve-status');
