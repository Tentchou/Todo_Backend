<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

            // Créer quelques todos pour l'utilisateur de test
            $todo1 = Todo::firstOrCreate(
                ['user_id' => $user->id, 'title' => 'Finish API development'],
                [
                    'description' => 'Complete all CRUD operations for Todos, Categories, and Tags.',
                    'due_date' => now()->addDays(3),
                    'priority' => 3, // High
                    'is_completed' => false,
                    'category_id' => $workCategory ? $workCategory->id : null,
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
                    'priority' => 2, // Medium
                    'is_completed' => false,
                    'category_id' => $personalCategory ? $personalCategory->id : null,
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
                    'priority' => 1, // Low
                    'is_completed' => true,
                    'completed_at' => now()->subDays(2),
                    'category_id' => $personalCategory ? $personalCategory->id : null,
                ]
            );
            // Pas de tags pour cette todo

            // Créer 10 todos supplémentaires avec des données aléatoires
            Todo::factory(10)->create([
                'user_id' => $user->id,
                'category_id' => function () use ($user) {
                    // Choisir une catégorie existante de l'utilisateur ou null
                    return Category::where('user_id', $user->id)->inRandomOrder()->first()?->id;
                }
            ])->each(function ($todo) use ($user) {
                // Attacher 0 à 2 tags aléatoires à chaque todo
                $tags = Tag::where('user_id', $user->id)->inRandomOrder()->limit(rand(0, 2))->get();
                $todo->tags()->attach($tags->pluck('id'));
            });
        }
    }
}
