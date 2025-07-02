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
            $table->string('pending_status')->nullable();
            $table->unsignedBigInteger('status_updated_by')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->unsignedBigInteger('status_approved_by')->nullable();
            $table->timestamp('status_approved_at')->nullable();
            $table->text('status_rejection_comment')->nullable();

            $table->foreign('status_updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('status_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropForeign(['status_updated_by']);
            $table->dropForeign(['status_approved_by']);
            $table->dropColumn([
                'pending_status',
                'status_updated_by',
                'status_updated_at',
                'status_approved_by',
                'status_approved_at',
                'status_rejection_comment'
            ]);
        });
    }
};
