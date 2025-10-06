<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $countries = Country::with('provinces')
            ->where('disabled', false)
            ->get();

        return response()->json($countries);
    }
}