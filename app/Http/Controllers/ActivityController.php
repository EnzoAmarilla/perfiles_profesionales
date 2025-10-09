<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return response()->json(["data" => Activity::all()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:activities,name',
        ]);

        $activity = Activity::create($request->all());
        return response()->json(["data" => $activity], 201);
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:activities,name,' . $activity->id,
        ]);

        $activity->update($request->all());
        return response()->json(["data" => $activity]);
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json(["message" => "Actividad eliminada con exito"]);
    }
}