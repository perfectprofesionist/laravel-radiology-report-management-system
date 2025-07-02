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
            $table->text('rejection_comment')->nullable()->after('notes_updated_at');
            $table->timestamp('rejected_at')->nullable()->after('rejection_comment');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['rejection_comment', 'rejected_at', 'rejected_by']);
        });
    }
};
