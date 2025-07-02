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
            $table->string('admin_access_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_listing', function (Blueprint $table) {
            $table->dropColumn(['admin_access_token', 'token_expires_at']);
        });
    }
};
