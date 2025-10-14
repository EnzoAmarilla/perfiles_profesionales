<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['userType', 'activities', 'locality.province'])->get();
        return response()->json(["data" => $users]);
    }

    public function show(User $user)
    {
        return response()->json($user->load(['userType', 'activities', 'locality.province']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'profile_picture' => 'nullable|string',
            'description' => 'nullable|string',
            'user_type_id' => 'required|exists:user_types,id',
            'locality_id' => 'nullable|exists:localities,id',
            'activities' => 'nullable|array',
            'activities.*' => 'exists:activities,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if (!empty($validated['activities'])) {
            $user->activities()->sync($validated['activities']);
        }

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'data' => $user->load(['userType', 'activities', 'locality.province']),
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes','email',Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6|nullable',
            'phone' => 'nullable|string|max:30',
            'profile_picture' => 'nullable|string',
            'description' => 'nullable|string',
            'user_type_id' => 'sometimes|exists:user_types,id',
            'locality_id' => 'nullable|exists:localities,id',
            'activities' => 'nullable|array',
            'activities.*' => 'exists:activities,id',
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['activities'])) {
            $user->activities()->sync($validated['activities']);
        }
        
        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user->load(['userType', 'activities', 'locality.province']),
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function getUserTypes()
    {
        $types = UserType::where('disabled', false)->get(['id', 'name', 'description']);
        return response()->json(["data" => $types]);
    }

    public function getDocumentTypes()
    {
        $types = DocumentType::where('disabled', false)->get(['id', 'code', 'name', 'description']);
        return response()->json(["data" => $types]);
    }
}