<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->uuid('request_uuid'); // UUID for associating files with a request
            $table->string('original_name');
            $table->string('file_name'); // The name of the file
            $table->string('file_url');  // URL to access the file
            $table->enum('type', [
                'patients_docs',
                'patients_supporting_files',
                'doctors_docs',
                'doctors_supporting_files',
                'Radiologists_docs',
                'Radiologists_supporting_files'
            ]);
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
