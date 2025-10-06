<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use Illuminate\Http\JsonResponse;

class LocalityController extends Controller
{
    public function index(): JsonResponse
    {
        $localities = Locality::with('province.country')
            ->where('disabled', false)
            ->get();

        return response()->json($localities);
    }

    public function byProvince($provinceId): JsonResponse
    {
        $localities = Locality::where('province_id', $provinceId)
            ->where('disabled', false)
            ->get();

        return response()->json($localities);
    }
}