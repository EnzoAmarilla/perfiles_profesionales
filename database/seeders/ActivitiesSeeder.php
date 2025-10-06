<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Albañil',
                'short_code' => 'ALB',
                'tags' => 'construcción, ladrillos, cemento',
                'code' => 'ACT001',
                'disabled' => false,
            ],
            [
                'name' => 'Carpintero',
                'short_code' => 'CAR',
                'tags' => 'madera, muebles, construcción',
                'code' => 'ACT002',
                'disabled' => false,
            ],
            [
                'name' => 'Electricista',
                'short_code' => 'ELE',
                'tags' => 'instalaciones, cables, energía',
                'code' => 'ACT003',
                'disabled' => false,
            ],
            [
                'name' => 'Plomero',
                'short_code' => 'PLO',
                'tags' => 'agua, caños, instalaciones',
                'code' => 'ACT004',
                'disabled' => false,
            ],
        ];

        foreach ($activities as $activity) {
            DB::table('activities')->updateOrInsert(
                ['code' => $activity['code']], // criterio único
                $activity
            );
        }
    }
}