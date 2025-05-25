<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Http\Requests\TodoRequest;
use App\Http\Resources\TodoResource;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $todos = auth()->user()->todos();

        // Recherche par titre ou description
        if ($request->has('search')) {
            $search = $request->input('search');
            $todos->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filtrage par catégorie
        if ($request->filled('category_id')) {
            $todos->where('category_id', $request->input('category_id'));
        }

        // Filtrage par tag
        if ($request->filled('tag_id')) {
            $tagId = $request->input('tag_id');
            $todos->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        // Filtrage par statut (complétée/non complétée)
        if ($request->filled('is_completed')) {
            $todos->where('is_completed', (bool)$request->input('is_completed'));
        }

        // Filtrage par priorité
        if ($request->filled('priority')) {
            $todos->where('priority', $request->input('priority'));
        }

        // Filtrage par date d'échéance (exemple: avant une certaine date)
        if ($request->filled('due_before')) {
            $todos->where('due_date', '<=', $request->input('due_before'));
        }

        // Tri (exemple: par date de création, date d'échéance, priorité)
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Assurez-vous que les colonnes de tri sont valides
        $validSortColumns = ['created_at', 'due_date', 'priority', 'title'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at'; // Fallback
        }
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

        $todos->orderBy($sortBy, $sortOrder);

        // Avec eager loading pour les relations
        $todos->with(['category', 'tags']);

        // Pagination
        $perPage = $request->input('per_page', 10);

        return TodoResource::collection($todos->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TodoRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        // Gérer la date de complétion si la tâche est marquée comme complétée
        if (isset($data['is_completed']) && $data['is_completed']) {
            $data['completed_at'] = now();
        }

        $todo = Todo::create($data);

        // Synchroniser les tags si des tags sont fournis
        if (isset($data['tags'])) {
            $todo->tags()->sync($data['tags']);
        }

        return new TodoResource($todo->load(['category', 'tags']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        $this->authorize('view', $todo); // Utilise la politique TodoPolicy
        return new TodoResource($todo->load(['category', 'tags']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TodoRequest $request, Todo $todo)
    {
        $this->authorize('update', $todo); // Utilise la politique TodoPolicy
        $data = $request->validated();

        // Gérer la date de complétion
        if (isset($data['is_completed'])) {
            if ($data['is_completed'] && !$todo->is_completed) {
                $data['completed_at'] = now();
            } elseif (!$data['is_completed'] && $todo->is_completed) {
                $data['completed_at'] = null;
            }
        }

        $todo->update($data);

        // Synchroniser les tags
        if (isset($data['tags'])) {
            $todo->tags()->sync($data['tags']);
        } else {
            $todo->tags()->detach(); // Si aucun tag n'est envoyé, on supprime tous les tags existants
        }

        return new TodoResource($todo->load(['category', 'tags']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo); // Utilise la politique TodoPolicy
        $todo->delete();
        return response()->json(null, 204);
    }
}
