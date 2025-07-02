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
        Schema::table('request_listing', function (Blueprint $table) {
            $table->string('notes_status')->default('approved')->after('notes');
            $table->text('pending_notes')->nullable()->after('notes_status');
            $table->timestamp('notes_approved_at')->nullable()->after('pending_notes');
            $table->unsignedBigInteger('notes_approved_by')->nullable()->after('notes_approved_at');
            $table->foreign('notes_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropForeign(['notes_approved_by']);
            $table->dropColumn(['notes_status', 'pending_notes', 'notes_approved_at', 'notes_approved_by']);
        });
    }
};
