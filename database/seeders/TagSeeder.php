<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if ($user) {
            Tag::firstOrCreate(['user_id' => $user->id, 'name' => 'Urgent']);
            Tag::firstOrCreate(['user_id' => $user->id, 'name' => 'Home']);
            Tag::firstOrCreate(['user_id' => $user->id, 'name' => 'Project A']);
            Tag::firstOrCreate(['user_id' => $user->id, 'name' => 'Meeting']);
        }
    }
}
