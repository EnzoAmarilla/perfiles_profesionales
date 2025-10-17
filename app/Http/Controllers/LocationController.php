<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\Locality;
use App\Models\ZipCode;

class LocationController extends Controller
{
    /**
     * Listado de países (con filtro opcional por nombre)
     */
    public function getCountries(Request $request)
    {
        $query = Country::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $countries = $query->orderBy('name')->get(['id', 'name']);
        return response()->json(["data" => $countries]);
    }

    /**
     * Listado de provincias (filtrado por country_id)
     */
    public function getStates(Request $request)
    {
        $query = State::query();

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $states = $query->orderBy('name')->get(['id', 'name', 'country_id']);
        return response()->json(["data" => $states]);
    }

    /**
     * Listado de localidades (filtrado por state_id)
     */
    public function getLocalities(Request $request)
    {
        $query = Locality::with(['zipCodes', 'state.country']);

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // --- PAGINACIÓN ---
        $page  = $request->get('page', 1);     // página actual (por defecto 1)
        $limit = $request->get('limit', 10);   // cantidad por página (por defecto 20)

        $localities = $query->orderBy('name')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => $localities->items(),
            'pagination' => [
                'current_page' => $localities->currentPage(),
                'per_page' => $localities->perPage(),
                'total' => $localities->total(),
                'last_page' => $localities->lastPage(),
            ]
        ]);
    }

    /**
     * Listado de códigos postales (filtrado por locality_id)
     */
    public function getZipCodes(Request $request)
    {
        $query = ZipCode::with('locality');

        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        // --- PAGINACIÓN ---
        $page  = $request->get('page', 1);     // página actual (por defecto 1)
        $limit = $request->get('limit', 10);   // cantidad por página (por defecto 20)

        $zipCodes = $query->orderBy('code')->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => $zipCodes->items(),
            'pagination' => [
                'current_page' => $zipCodes->currentPage(),
                'per_page' => $zipCodes->perPage(),
                'total' => $zipCodes->total(),
                'last_page' => $zipCodes->lastPage(),
            ]
        ]);
    }
}