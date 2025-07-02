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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_listing_id');
            $table->string('stripe_charge_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10);
            $table->string('status', 50);
            $table->string('payment_method', 100)->nullable();
            $table->string('receipt_url', 512)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamps();
            $table->foreign('request_listing_id')->references('id')->on('request_listing')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
