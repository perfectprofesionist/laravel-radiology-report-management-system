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
            $table->unsignedBigInteger('notes_updated_by')->nullable()->after('notes_approved_by');
            $table->timestamp('notes_updated_at')->nullable()->after('notes_updated_by');
            $table->foreign('notes_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropForeign(['notes_updated_by']);
            $table->dropColumn(['notes_updated_by', 'notes_updated_at']);
        });
    }
};
