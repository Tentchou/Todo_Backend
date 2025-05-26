<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Todo;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if ($user) {
            $workCategory = Category::where('user_id', $user->id)->where('name', 'Work')->first();
            $personalCategory = Category::where('user_id', $user->id)->where('name', 'Personal')->first();

            $urgentTag = Tag::where('user_id', $user->id)->where('name', 'Urgent')->first();
            $homeTag = Tag::where('user_id', $user->id)->where('name', 'Home')->first();

            // Créer quelques todos manuellement
            $todo1 = Todo::firstOrCreate(
                ['user_id' => $user->id, 'title' => 'Finish API development'],
                [
                    'description' => 'Complete all CRUD operations for Todos, Categories, and Tags.',
                    'due_date' => now()->addDays(3),
                    'priority' => 3,
                    'is_completed' => false,
                    'category_id' => $workCategory?->id,
                ]
            );
            if ($urgentTag) {
                $todo1->tags()->syncWithoutDetaching([$urgentTag->id]);
            }

            $todo2 = Todo::firstOrCreate(
                ['user_id' => $user->id, 'title' => 'Buy groceries'],
                [
                    'description' => 'Milk, eggs, bread, fruits.',
                    'due_date' => now()->addDay(),
                    'priority' => 2,
                    'is_completed' => false,
                    'category_id' => $personalCategory?->id,
                ]
            );
            if ($homeTag) {
                $todo2->tags()->syncWithoutDetaching([$homeTag->id]);
            }

            $todo3 = Todo::firstOrCreate(
                ['user_id' => $user->id, 'title' => 'Plan weekend trip'],
                [
                    'description' => 'Research destinations and book accommodation.',
                    'due_date' => now()->addWeeks(1),
                    'priority' => 1,
                    'is_completed' => true,
                    'completed_at' => now()->subDays(2),
                    'category_id' => $personalCategory?->id,
                ]
            );

            // Créer 10 todos aléatoires
            Todo::factory(10)->create([
                'user_id' => $user->id,
                'title' => fake()->sentence(), // ✅ ajout du titre obligatoire
                'category_id' => Category::where('user_id', $user->id)->inRandomOrder()->first()?->id,
            ])->each(function ($todo) use ($user) {
                $tags = Tag::where('user_id', $user->id)->inRandomOrder()->limit(rand(0, 2))->get();
                $todo->tags()->attach($tags->pluck('id'));
            });
        }
    }
}
