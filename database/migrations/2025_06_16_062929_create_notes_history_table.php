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
        Schema::create('notes_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_uuid');
            $table->foreign('request_uuid')
                  ->references('uuid')
                  ->on('request_listing')
                  ->onDelete('cascade');
            $table->text('notes_content');
            $table->string('status'); // pending, approved, rejected
            $table->text('comment')->nullable(); // approval or rejection comment
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            $table->timestamps();
        });

        // Add pending_status_value column to request_listing table
        Schema::table('request_listing', function (Blueprint $table) {
            $table->string('pending_status_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes_history');
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropColumn('pending_status_value');
        });
    }
};
