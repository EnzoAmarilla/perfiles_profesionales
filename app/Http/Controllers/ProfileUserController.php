<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileUser;

class ProfileUserController extends Controller
{
    public function index()
    {
        return response()->json(["data" => ProfileUser::all()]);
    }

    public function show(ProfileUser $profileUser)
    {
        return response()->json(["data" => $profileUser]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:profiles_users,name',
        ]);

        $profile = ProfileUser::create($validated);
        return response()->json(["data" => $profile], 201);
    }

    public function update(Request $request, ProfileUser $profileUser)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:profiles_users,name,' . $profileUser->id,
        ]);

        $profileUser->update($validated);
        return response()->json(["data" => $profileUser]);
    }

    public function destroy(ProfileUser $profileUser)
    {
        $profileUser->delete();
        return response()->json(["message" => "Perfil de usuario eliminado con exito"]);
    }
}
