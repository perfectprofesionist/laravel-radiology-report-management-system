<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\RequestListing;
use App\Models\Modality;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RequestListingFactory extends Factory
{
    public function definition(): array
    {
        $randomModality = Modality::inRandomOrder()->first();

        // Generate exam_id like FUS08743 or FUSK8743 if already exists
        $baseId = 'FUS' . str_pad(mt_rand(1000, 99999), 5, '0', STR_PAD_LEFT);
        $examId = $baseId;

        if (RequestListing::where('exam_id', $examId)->exists()) {
            $randomLetter = chr(mt_rand(65, 90)); // A-Z
            $examId = 'FUS' . $randomLetter . substr($baseId, 3);
        }

        return [
            'uuid' => Str::uuid(),
            'patient_name' => $this->faker->name(),
            'patient_dob' => $this->faker->date(),
            'clinical_details' => $this->faker->paragraph(),
            'scan_file' => null,
            'scan_date' => $this->faker->date(),
            'modality' => $randomModality ? $randomModality->name : 'Unknown',
            'status' => $this->faker->randomElement(['Pending', 'Assigned', 'Incident', 'Completed']),
            'appointment_date' => $this->faker->optional()->date(),
            'user_id' => User::inRandomOrder()->value('id'),
            'exam_id' => $examId, // unique formatted ID
            'question' => $this->faker->optional()->paragraph(),
            'notes' => $this->faker->optional()->paragraph(),
            'payment_amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid', 'refunded']),
        ];
    }
}
