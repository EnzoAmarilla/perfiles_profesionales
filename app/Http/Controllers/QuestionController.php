<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Listado con filtros
    public function index(Request $request)
    {
        $query = Question::with('user');

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
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        // if ($request->filled('message')) {
        //     $query->where('message', 'like', '%' . $request->message . '%');
        // }

        return response()->json([
            'data' => $query->orderBy('created_at', 'desc')->get()
        ]);
    }

    // Crear nueva pregunta
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'message' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $question = Question::create($validated);

        return response()->json([
            'message' => 'Pregunta creada exitosamente',
            'data' => $question,
        ]);
    }

    // Mostrar una pregunta específica
    public function show($id)
    {
        $question = Question::with('user')->findOrFail($id);
        return response()->json(['data' => $question]);
    }

    // Actualizar (por ejemplo, para responder o publicar)
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'message' => 'nullable|string',
            'answer' => 'nullable|string',
            'published' => 'nullable|boolean',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Pregunta actualizada exitosamente',
            'data' => $question,
        ]);
    }

    // Eliminar una pregunta
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Pregunta eliminada exitosamente']);
    }
}