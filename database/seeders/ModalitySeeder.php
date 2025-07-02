<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modality;

class ModalitySeeder extends Seeder
{
    public function run(): void
    {
        $modalities = [
            ['name' => 'Upper Jaw', 'price' => 12],
            ['name' => 'Lower Jaw', 'price' => 15],
            ['name' => 'Small FOV', 'price' => 20],
        ];

        foreach ($modalities as $modality) {
            Modality::updateOrCreate(['name' => $modality['name']], $modality);
        }
    }
}
