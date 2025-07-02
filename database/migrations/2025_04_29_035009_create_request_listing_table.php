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
        Schema::create('request_listing', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('patient_name')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_postcode')->nullable();
            $table->string('patient_address')->nullable();
            $table->string('patient_email')->nullable();
            $table->string('clinical_history')->nullable();
            $table->date('patient_dob')->nullable();
            $table->date('appointment')->nullable();
            $table->text('clinical_details')->nullable();
            $table->string('scan_file')->nullable();
            $table->date('scan_date')->nullable();
            $table->string('modality')->nullable();
            $table->string('status')->default('Pending'); 
            $table->date('appointment_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('exam_id')->unique()->nullable();
            $table->text('question')->nullable();
            $table->text('notes')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->decimal('payment_amount', 10, 2)->default(0); 
            $table->string('payment_status')->default('unpaid'); 
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_listing');
    }
};
