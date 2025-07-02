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
            $table->unsignedBigInteger('status_rejected_by')->nullable()->after('status_rejection_comment');
            $table->timestamp('status_rejected_at')->nullable()->after('status_rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropColumn(['status_rejected_by', 'status_rejected_at']);
        });
    }
};
