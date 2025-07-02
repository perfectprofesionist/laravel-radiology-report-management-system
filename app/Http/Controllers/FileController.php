<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\RequestListing;

/**
 * FileController handles file upload, management, and serving operations for the radiology report system.
 * Supports chunked file uploads for large files and provides file CRUD operations.
 */
class FileController extends Controller
{

    /**
     * Handle chunked file uploads for large files.
     * Supports progress tracking and handles both partial and complete uploads.
     */
    public function uploadfiles(Request $request)
    {
        // Create the file receiver to handle chunked uploads
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // Check if the upload is successful and file exists
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // Receive and process the uploaded file chunks
        $save = $receiver->receive();

        // Check if the upload has finished (all chunks received)
        if ($save->isFinished()) {
            // Save the complete file and return file info
            return $this->saveFile($save->getFile(), $request->request_uuid);
        }

        // Get the upload handler for progress tracking
        $handler = $save->handler();

        // Return upload progress percentage for frontend progress bars
        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }

    /**
     * Handle chunked file uploads with specific file type categorization.
     * Similar to uploadfiles but includes type parameter for file categorization.
     */
    public function uploadFilesWithType(Request $request ,$type)
    {
        // Create the file receiver to handle chunked uploads
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // Check if the upload is successful and file exists
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // Receive and process the uploaded file chunks
        $save = $receiver->receive();

        // Check if the upload has finished (all chunks received)
        if ($save->isFinished()) {
            // Save the complete file with type categorization and return file info
            return $this->saveFile($save->getFile(), $request->request_uuid, $type, $save->getClientOriginalName());
        }

        // Get the upload handler for progress tracking
        $handler = $save->handler();

        // Return upload progress percentage for frontend progress bars
        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }
    

    /**
     * Save uploaded file to storage and create database record.
     * Handles file storage, metadata creation, and error handling.
     */
    protected function saveFile(UploadedFile $file, $requestUuid , $type = null, $originalName = null)
    {

        // Generate a unique filename to prevent conflicts
        $fileName = $this->createFilename($file);

        // Define the file storage path in the private uploads directory
        $filePath = "private/uploads/";

        // Save the file to the public storage disk
        $fileStored = Storage::disk('public')->putFileAs($filePath, $file, $fileName);

        // Check if the file was successfully stored
        if (!$fileStored) {
            \Log::error("File failed to upload: " . $fileName);
            return response()->json(['error' => 'File upload failed'], 500);
        }

        // Generate the public URL for accessing the uploaded file
        $fileUrl = Storage::disk('public')->url($filePath . $fileName);

        // Store the file metadata in the database for tracking
        try {
            $fileRecord = File::create([
                'request_uuid' => $requestUuid, // UUID to associate with this request
                'original_name' => $originalName,
                'file_name' => $fileName,
                'file_url' => $filePath . $fileName, // URL for accessing the uploaded file
                'type' => $type ,
            ]);
            \Log::info("File record saved successfully: " . $fileRecord->id);
        } catch (\Exception $e) {
            \Log::error("Error saving file record: " . $e->getMessage());
            return response()->json(['error' => 'Database record saving failed'], 500);
        }

        // Return the file details as a response for frontend processing
        return response()->json([
            'path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'name' => $fileName,
            'file_url' => $fileUrl, // Provide the URL of the uploaded file
             'type' => $type ,
        ]);
    }

    /**
     * Generate a unique filename to prevent file conflicts.
     * Adds timestamp and random hash to original filename.
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // Filename without extension

        // Add timestamp hash to make the filename unique and prevent conflicts
        $filename .= "_" . md5(time() . rand()) . "." . $extension;

        return $filename;
    }

    /**
     * Remove uploaded file from storage and database.
     * Handles file deletion, database cleanup, and request listing updates.
     */
    public function removeUploadedFile(Request $request)
    {

        // Validate the incoming request for file_path and optionally request_uuid
        $validated = $request->validate([
            'file_path' => 'required|string',
            'request_uuid' => 'nullable|uuid',
        ]);
        
        $filePath = $validated['file_path'];
        $path ="public/private/uploads/" . $filePath ;
        $requestUuid = $validated['request_uuid'] ?? null;

        // Extract filename from path for database lookup
        $fileName = basename($filePath);
        $fullPath = storage_path("app/" . $path);

        // Check if file exists in storage
        if (file_exists($fullPath)) {
            // Attempt to delete the physical file
            if (unlink($fullPath)) {
                // Build the database query to find and delete file record
                $query = File::where('file_url', 'like', "%{$fileName}");

                // Add request UUID filter if provided
                if ($requestUuid) {
                    $query->where('request_uuid', $requestUuid);
                }

                $deletedCount = $query->delete();
                
                // If UUID is provided, clear the scan_file field in the request listing
                if ($requestUuid) {
                    // Update the request listing to remove file reference
                    $updated = RequestListing::where('uuid', $requestUuid)
                        ->update(['scan_file' => '']);
                }

                if ($deletedCount) {
                    return response()->json(['success' => true, 'message' => 'File removed successfully']);
                } else {
                    return response()->json(['success' => false, 'message' => 'File record not found in database'], 404);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'File could not be deleted'], 500);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }
    }

    /**
     * Retrieve all files associated with a specific request UUID.
     * Returns file metadata for display or processing.
     */
    public function showFiles($request_uuid)
    {
        // Get all files for the given request_uuid
        $files = File::where('request_uuid', $request_uuid)->get();

        return response()->json(['files' => $files]);
    }

    /**
     * Generate HTML list of files with specific type for frontend display.
     * Includes file links and delete buttons with proper escaping.
     */
    public function showFilesWithType($request_uuid, $type)
    {
        // Get files filtered by request UUID and type
        $files = File::where([
            'request_uuid' => $request_uuid,
            'type' => $type
        ])->get();

        // Build HTML list of files with download and delete options
        $html = '<ol>'; 

        foreach ($files as $file) {
            // Generate file access URL
            $url = route('scan.file', [$file->file_name]);
            $name = htmlspecialchars($file->file_name);
            $filePath = htmlspecialchars($file->file_name);
            $requestUuidEscaped = htmlspecialchars($request_uuid);
            $typeEscaped = htmlspecialchars($type);
            $originalName = htmlspecialchars($file->original_name);
            
            // Create HTML list item with file link and delete button
            $html .= "<li>
                          
                            <a target=\"_blank\" href=\"{$url}\" class=\"text-muted\">{$originalName}</a>
                            <a href=\"#\" 
                            class=\"text-danger ms-2 remove-file-link\" 
                            data-file-path=\"{$filePath}\" 
                            data-request-uuid=\"{$requestUuidEscaped}\" 
                            data-type=\"{$typeEscaped}\"
                            style=\"cursor:pointer;\">
                            <i class=\"fa fa-trash\" aria-hidden=\"true\"></i>
                        </a>
                    </li>";
        }
        $html .= '</ol>';

        // Commented out download button option
        //   <a href=\"{$url}\" download class=\"text-success me-2\" title=\"Download\">
        //                         <i class=\"fa fa-download\"></i>
        //                     </a>

        // Return HTML response for AJAX requests
        return response($html, 200)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Serve file for download by filename.
     * Provides direct file download functionality.
     */
    public function downloadFile($filename)
    {
        // Construct full file path
        $path = storage_path('app/public/' . $filename);
        
        // Check if file exists and serve for download
        if (file_exists($path)) {
            return response()->download($path);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

}


