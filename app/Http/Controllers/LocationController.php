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
        $query = Locality::query();

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $localities = $query->orderBy('name')->get(['id', 'name', 'state_id']);
        return response()->json(["data" => $localities]);
    }

    /**
     * Listado de códigos postales (filtrado por locality_id)
     */
    public function getZipCodes(Request $request)
    {
        $query = ZipCode::query();

        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        $zipCodes = $query->orderBy('code')->get(['id', 'code', 'locality_id']);
        return response()->json(["data" => $zipCodes]);
    }
}