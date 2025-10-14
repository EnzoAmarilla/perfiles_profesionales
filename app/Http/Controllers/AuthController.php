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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'profile_picture' => 'nullable|string',
            'description' => 'nullable|string',
            'user_type_id' => 'required|exists:user_types,id',
            'locality_id' => 'nullable|exists:localities,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_picture' => $validated['profile_picture'] ?? null,
            'description' => $validated['description'] ?? null,
            'user_type_id' => $validated['user_type_id'],
            'locality_id' => $validated['locality_id'] ?? null,
        ]);

        // Generar token JWT automáticamente
        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => (int) auth('api')->factory()->getTTL() * 60,
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