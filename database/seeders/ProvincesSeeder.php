<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Country;

class ProvincesSeeder extends Seeder
{
    public function run(): void
    {
        $argentina = Country::where('name', 'Argentina')->first();
        $peru = Country::where('name', 'Perú')->first();

        $provinces = [
            ['name' => 'Buenos Aires', 'short_code' => 'BA', 'country_id' => $argentina->id, 'disabled' => false],
            ['name' => 'Córdoba', 'short_code' => 'CBA', 'country_id' => $argentina->id, 'disabled' => false],
            ['name' => 'Lima', 'short_code' => 'LIM', 'country_id' => $peru->id, 'disabled' => false],
            ['name' => 'Cusco', 'short_code' => 'CUS', 'country_id' => $peru->id, 'disabled' => false],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['name' => $province['name'], 'country_id' => $province['country_id']],
                $province
            );
        }
    }
}