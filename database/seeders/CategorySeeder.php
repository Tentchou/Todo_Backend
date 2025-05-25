<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if ($user) {
            Category::firstOrCreate(['user_id' => $user->id, 'name' => 'Work']);
            Category::firstOrCreate(['user_id' => $user->id, 'name' => 'Personal']);
            Category::firstOrCreate(['user_id' => $user->id, 'name' => 'Shopping']);
            Category::firstOrCreate(['user_id' => $user->id, 'name' => 'Health']);
        }
    }
}
