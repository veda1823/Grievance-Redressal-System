<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Academic',
            'Hostel & Accommodation',
            'Infrastructure & Maintenance',
            'Mess & Canteen',
            'Library',
            'Administration & Fees',
            'Other',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
