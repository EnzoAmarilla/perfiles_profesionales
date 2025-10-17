<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Paginación con valores por defecto
        $page  = $request->get('page');
        $limit = $request->get('limit');

        $activities = $query->orderBy('name')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => $activities->items(),
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'last_page' => $activities->lastPage(),
            ]
        ]);
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