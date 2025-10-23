<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'description' => 'nullable|string',
            'user_type_id' => 'required|exists:user_types,id',
            'locality_id' => 'nullable|exists:localities,id',
            'zone' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = new User($request->except('photo'));
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Guardar actividades si las hay
        if (!empty($request['activities'])) {
            $user->activities()->sync($request['activities']);
        }

        // Si viene foto, guardarla
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $user->save();
        }

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user->load(['userType', 'locality']),
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string']
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('api')->user()->load(['userType', 'locality']));
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    protected function respondWithToken($token)
    {
        $expire_in = config('jwt.ttl');
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expire_in * 60, // segundos,
            'user' => auth('api')->user()->load(['userType', 'locality'])
        ]);
    }
}