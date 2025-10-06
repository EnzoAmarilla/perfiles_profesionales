<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locality;
use App\Models\Province;

class LocalitiesSeeder extends Seeder
{
    public function run(): void
    {
        $buenosAires = Province::where('name', 'Buenos Aires')->first();
        $cordoba = Province::where('name', 'Córdoba')->first();
        $lima = Province::where('name', 'Lima')->first();

        $localities = [
            ['name' => 'La Plata', 'short_code' => 'LAP', 'province_id' => $buenosAires?->id, 'disabled' => false],
            ['name' => 'Mar del Plata', 'short_code' => 'MDP', 'province_id' => $buenosAires?->id, 'disabled' => false],
            ['name' => 'Villa Carlos Paz', 'short_code' => 'VCP', 'province_id' => $cordoba?->id, 'disabled' => false],
            ['name' => 'Miraflores', 'short_code' => 'MIR', 'province_id' => $lima?->id, 'disabled' => false],
        ];

        foreach ($localities as $locality) {
            if ($locality['province_id']) {
                Locality::updateOrCreate(
                    ['name' => $locality['name'], 'province_id' => $locality['province_id']],
                    $locality
                );
            }
        }
    }
}