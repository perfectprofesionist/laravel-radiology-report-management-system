<?php

// database/seeders/ImportSqlDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlDataSeeder extends Seeder
{
    public function run(): void
{
    $files = [
        'regions.sql',
        'subregions.sql',
        'countries.sql',
        'states.sql',
        'cities.sql',
    ];

    foreach ($files as $file) {
        $path = database_path('data/' . $file);

        if ($file === 'cities.sql') {
            $this->importSqlInChunks($path); // ⬅️ Use chunk method only for large files
        } else {
            if (File::exists($path)) {
                DB::unprepared(File::get($path));
                $this->command->info(" Imported: {$file}");
            } else {
                $this->command->warn(" Missing: {$file}");
            }
        }
    }
}
    protected function importSqlInChunks(string $filePath, int $chunkSize = 1000): void
{
    if (!file_exists($filePath)) {
        $this->command->warn(" File not found: {$filePath}");
        return;
    }

    $sql = '';
    $count = 0;

    $handle = fopen($filePath, 'r');

    while (($line = fgets($handle)) !== false) {
        // Skip comments and empty lines
        if (strpos($line, '--') === 0 || trim($line) === '') {
            continue;
        }

        $sql .= $line;

        if (substr(trim($line), -1) === ';') {
            DB::unprepared($sql);
            $sql = '';
            $count++;

            if ($count % $chunkSize === 0) {
                $this->command->info("Imported {$count} queries from " . basename($filePath));
            }
        }
    }

    fclose($handle);

    $this->command->info(" Finished importing: " . basename($filePath));
}

}
