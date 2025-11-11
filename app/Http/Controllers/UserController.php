<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Question;
use App\Models\Review;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // public function index()
    // {
    //     $users = User::with(['userType', 'activities', 'locality.state'])->get();
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

    public function professionals(Request $request)
    {
        $query = User::with(['userType', 'activities', 'locality.state'])
            ->where('user_type_id', 2) // solo profesionales
            ->where('is_active', 1) // solo activos
            ->withCount('reviews')
            ->withAvg('reviews', 'value'); // promedio de valoraciones

        // --- FILTRO GLOBAL DE BÚSQUEDA ---
        if ($request->filled('search')) {
            $search = strtolower($request->search);

            $query->where(function ($q) use ($search) {
                // nombre o apellido
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])

                // localidad
                ->orWhereHas('locality', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                })

                // provincia
                ->orWhereHas('locality.state', function ($q3) use ($search) {
                    $q3->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                })

                // actividad
                ->orWhereHas('activities', function ($q4) use ($search) {
                    $q4->whereRaw('LOWER(activities.name) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        // --- FILTRO POR ACTIVIDAD (ID) ---
        if ($request->filled('activity_id')) {
            $activityId = $request->get('activity_id');
            $query->whereHas('activities', function ($q) use ($activityId) {
                $q->where('activities.id', $activityId);
            });
        }

        // --- FILTRO POR LOCALIDAD ---
        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        // --- FILTRO POR PROVINCIA ---
        if ($request->filled('state_id')) {
            $query->whereHas('locality.state', function ($q) use ($request) {
                $q->where('id', $request->state_id);
            });
        }

        // --- ORDEN POR VALORIZACIÓN ---
        $query->orderByDesc('reviews_avg_value')
            ->orderByDesc('reviews_count');

        // --- PAGINACIÓN ---
        $page  = $request->get('page');
        $limit = $request->get('limit');

        $users = $query->orderBy('id', 'desc');

        if ($page && $limit) {
            $users = $users->paginate($limit, ['*'], 'page', $page);

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

        // Si no hay paginación
        $users = $users->get();

        return response()->json(["data" => $users]);
    }

    public function show(User $user)
    {
        return response()->json(["data" => $user->load(['userType', 'activities', 'locality.state', 'questions', 'reviews'])]);
    }
 
    public function show_professionals(User $professional)
    {
        return response()->json(["data" => $professional->load(['userType', 'activities', 'locality.state', 'questions', 'reviews'])]);
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
            'data' => $user->load(['userType', 'activities', 'locality.state']),
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
            'data' => $user->load(['userType', 'activities', 'locality.state']),
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

    public function uploadProfilePictureProfessional(Request $request)
    {
        $user = User::find(Auth::user()->id);

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
            'photo_url' => asset($path),
        ], 200);
    }

    public function setActiveStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $user = User::findOrFail($id);
        $user->is_active = $request->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'Usuario activado.' : 'Usuario desactivado.',
            'data' => $user
        ]);
    }

    public function get_reviews_professional(Request $request, $professional = null) 
    {
        $query = Review::with('user')->where('user_id', $professional ?? Auth::id());

        // --- PAGINACIÓN OPCIONAL ---
        $page  = $request->get('page');   // página actual
        $limit = $request->get('limit');  // cantidad por página

        $reviews = $query->orderBy('id', 'DESC');

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
            // Si no se envía paginación → traer todo
            $reviews = $reviews->get();
            return response()->json(["data" => $reviews]);
        }
    }

    public function get_questions_professional(Request $request, $professional = null)
    {
        $query = Question::with('user')->where('user_id', $professional ?? Auth::id());

        // --- PAGINACIÓN OPCIONAL ---
        $page  = $request->get('page'); 
        $limit = $request->get('limit');

        $questions = $query->orderBy('id', 'DESC');

        if ($page && $limit) {
            $questions = $questions->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'data' => $questions->items(),
                'pagination' => [
                    'current_page' => $questions->currentPage(),
                    'per_page' => $questions->perPage(),
                    'total' => $questions->total(),
                    'last_page' => $questions->lastPage(),
                ]
            ]);
        } else {
            $questions = $questions->get();
            return response()->json(["data" => $questions]);
        }
    }

    public function get_professional_detail()
    {
        $user = User::with(['userType', 'activities', 'locality.state'])->find(Auth::id());

        return response()->json(["data" => $user]);
    }

    public function professionals_respond_review(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // validar que solamente el usuario prof dueño pueda contestar
        if($review->user_id != Auth::user()->id)
            return response()->json(['message' => 'Usuario no authorizado'], 400);
        
        $validated = $request->validate([
            'answer' => 'nullable|string',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Valoración actualizada exitosamente',
            'data' => $review,
        ]);
    }

    public function professionals_respond_question(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'answer' => 'nullable|string',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Comentario actualizado exitosamente',
            'data' => $question,
        ]);
    }

    public function professional_update_profile(Request $request)
    {
        $user = User::find(Auth::user()->id);

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
            'data' => $user->load(['userType', 'activities', 'locality.state']),
        ]);
    }
}