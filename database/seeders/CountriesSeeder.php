<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Argentina', 'short_code' => 'AR', 'disabled' => false],
            ['name' => 'Perú', 'short_code' => 'PE', 'disabled' => false],
            ['name' => 'Chile', 'short_code' => 'CL', 'disabled' => false],
            ['name' => 'Brasil', 'short_code' => 'BR', 'disabled' => false],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['name' => $country['name']],
                $country
            );
        }
    }
}