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
    // public function index()
    // {
    //     $users = User::with(['userType', 'activities', 'locality.province'])->get();
    //     return response()->json(["data" => $users]);
    // }

    public function index(Request $request)
    {
        $query = User::with(['userType', 'activities', 'locality.state']);

        // Filtro por nombre o apellido
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->name . '%')
                ->orWhere('last_name', 'like', '%' . $request->name . '%');
            });
        }

        // Filtro por email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filtro por rol (user_type_id)
        if ($request->filled('user_type_id')) {
            $query->where('user_type_id', $request->user_type_id);
        }

        // Filtro por provincia
        if ($request->filled('province_id')) {
            $query->whereHas('locality.state', function ($q) use ($request) {
                $q->where('id', $request->province_id);
            });
        }

        // Filtro por localidad
        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        // Filtro por estado (si existe campo status)
        // if ($request->filled('status')) {
        //     $query->where('status', $request->status);
        // }

        // Filtro por fecha de creación (rango)
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        // Filtro por último acceso (si tenés campo last_login_at)
        // if ($request->filled('last_login')) {
            // $query->whereNotNull('last_login_at');
            // Ejemplo: last_login = "recent" o algo similar
        // }

        $users = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'data' => $users
        ]);
    }

    public function show(User $user)
    {
        return response()->json($user->load(['userType', 'activities', 'locality.province']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile_picture' => 'nullable|string',
            'description' => 'nullable|string',
            'user_type_id' => 'required|exists:user_types,id',
            'locality_id' => 'nullable|exists:localities,id',
            'activities' => 'nullable|array',
            'activities.*' => 'exists:activities,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($request->all());

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
            'email' => ['sometimes','email',Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6|nullable',
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

        $user->update($request->all());

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