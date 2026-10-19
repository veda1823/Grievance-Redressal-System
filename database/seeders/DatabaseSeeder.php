<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => env('STAFF_EMAIL', 'staff@example.com')],
            [
                'name' => env('STAFF_NAME', 'Staff Administrator'),
                'password' => Hash::make(env('STAFF_PASSWORD', 'password')),
                'role' => 'staff',
                'email_verified_at' => now(),
            ],
        );
    }
}
