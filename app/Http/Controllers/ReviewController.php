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

        return response()->json([
            'data' => $query->orderBy('created_at', 'desc')->get()
        ]);
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