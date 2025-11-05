<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Listado con filtros
    public function index(Request $request)
    {
        $query = Review::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('published')) {
            $query->where('published', $request->published);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('comment', 'like', '%' . $request->search . '%');
            });
        }

        // --- PAGINACIÓN OPCIONAL ---
        $page  = $request->get('page');   // página (default null = sin paginar)
        $limit = $request->get('limit');  // por página (default null = sin paginar)

        $reviews = $query->orderBy('created_at', 'desc');

        if ($page && $limit) {
            $reviews = $reviews->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'data' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                    'last_page' => $reviews->lastPage(),
                ]
            ]);
        } else {
            $reviews = $reviews->get();
            return response()->json(["data" => $reviews]);
        }
    }

    // Crear nueva valorización
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'value' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'title' => 'nullable|string',
            'proyect_type' => 'nullable|string',
        ]);

        $review = Review::create($validated);

        return response()->json([
            'message' => 'Valoración creada exitosamente',
            'data' => $review,
        ]);
    }

    // Mostrar una valoración específica
    public function show($id)
    {
        $review = Review::with('user')->findOrFail($id);
        return response()->json(['data' => $review]);
    }

    // Actualizar (por ejemplo, para responder o publicar)
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'nullable|string',
            'answer' => 'nullable|string',
            'published' => 'nullable|boolean',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Valoración actualizada exitosamente',
            'data' => $review,
        ]);
    }

    // Eliminar una valoración
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Valoración eliminada exitosamente']);
    }
}