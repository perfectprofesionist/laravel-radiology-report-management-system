<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        $this->call([
            ImportSqlDataSeeder::class,
            ModalitySeeder::class,
            // other seeders...
        ]);
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $subAdminRole = Role::firstOrCreate(['name' => 'sub-admin']);

        // Create admin user with UUID
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'uuid' => (string) Str::uuid(),  // Assign UUID
                'username' => 'Admin',
                'is_active' => true,
                'password' => bcrypt('password1234'),
            ]
        );
        // Set meta for admin
        $adminMeta = [
            'first_name' => 'Super',
            'last_name'  => 'Admin',
        ];
        foreach ($adminMeta as $key => $value) {
            $admin->setMeta($key, $value);
        }
        $admin->assignRole($adminRole);

        $user = User::firstOrCreate(
            ['email' => 'user111@gmail.com'],  // Use email unique for user
            [
                'uuid' => (string) Str::uuid(),
                'username' => 'user111',
                'is_active' => true,
                'password' => bcrypt('password'),
            ]
        );
        $user->assignRole($subAdminRole);

        
        // RequestListing::factory()->count(50)->create();

    }
}
