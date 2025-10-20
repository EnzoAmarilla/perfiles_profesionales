<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $query = User::with(['userType', 'activities', 'locality.state'])
                ->withCount('reviews')            // cantidad total de reviews
                ->withAvg('reviews', 'value');    // promedio de valoraciones (campo value en la tabla reviews);

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

        // --- PAGINACIÓN ---
        $page  = $request->get('page');     // página actual (default 1)
        $limit = $request->get('limit');   // cantidad por página (default 10)

        
        $users = $query->orderBy('id', 'desc');

        if($page && $limit){
            $users = $users->paginate($limit, ['*'], 'page', $page);
        }else{
            $users = $users->get();
            return response()->json(["data" => $users]);
        }

        // Redondeamos el promedio de reviews en cada item
        $users->getCollection()->transform(function ($user) {
            $user->reviews_avg_value = $user->reviews_avg_value
                ? number_format($user->reviews_avg_value, 1)
                : null;
            return $user;
        });

        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ]
        ]);

    }

    public function show(User $user)
    {
        return response()->json(["data" => $user->load(['userType', 'activities', 'locality.province', 'questions', 'reviews'])]);
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
            'password' => 'nullable|string|min:6',
            'profile_picture' => 'nullable|string',
            'description' => 'nullable|string',
            'user_type_id' => 'sometimes|exists:user_types,id',
            'locality_id' => 'nullable|exists:localities,id',
            'activities' => 'nullable|array',
            'activities.*' => 'exists:activities,id',
        ]);

        // Tomamos todos los datos del request
        $data = $request->all();

        // Si viene password vacía o nula, la eliminamos
        if (!isset($data['password']) || $data['password'] === '') {
            unset($data['password']);
        } else {
            // Si viene con texto, la encriptamos
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

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

    public function uploadProfilePicture(Request $request, User $user)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB máx
        ]);

        // Eliminar imagen anterior si existe
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Guardar nueva imagen
        $path = $request->file('photo')->store('profile_pictures', 'public');

        // Guardar en DB
        $user->profile_picture = $path;
        $user->save();

        // Retornar URL pública
        return response()->json([
            'message' => 'Foto actualizada correctamente',
            'photo_url' => asset('storage/' . $path),
        ], 200);
    }

}