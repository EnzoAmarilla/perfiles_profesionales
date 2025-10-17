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
            [
                'name' => 'Gasista',
                'short_code' => 'GAS',
                'tags' => 'gas, calefacción, instalaciones',
                'code' => 'ACT005',
                'disabled' => false,
            ],
            [
                'name' => 'Pintor',
                'short_code' => 'PIN',
                'tags' => 'pintura, interiores, paredes',
                'code' => 'ACT006',
                'disabled' => false,
            ],
            [
                'name' => 'Cerrajero',
                'short_code' => 'CER',
                'tags' => 'llaves, cerraduras, seguridad',
                'code' => 'ACT007',
                'disabled' => false,
            ],
            [
                'name' => 'Jardinero',
                'short_code' => 'JAR',
                'tags' => 'parques, césped, plantas',
                'code' => 'ACT008',
                'disabled' => false,
            ],
            [
                'name' => 'Mecánico',
                'short_code' => 'MEC',
                'tags' => 'autos, motores, reparación',
                'code' => 'ACT009',
                'disabled' => false,
            ],
            [
                'name' => 'Tapicero',
                'short_code' => 'TAP',
                'tags' => 'sillas, sofás, telas',
                'code' => 'ACT010',
                'disabled' => false,
            ],
            [
                'name' => 'Soldador',
                'short_code' => 'SOL',
                'tags' => 'metal, estructuras, herrería',
                'code' => 'ACT011',
                'disabled' => false,
            ],
            [
                'name' => 'Yesero',
                'short_code' => 'YES',
                'tags' => 'revoque, paredes, interiores',
                'code' => 'ACT012',
                'disabled' => false,
            ],
            [
                'name' => 'Vidriero',
                'short_code' => 'VID',
                'tags' => 'vidrios, ventanas, aberturas',
                'code' => 'ACT013',
                'disabled' => false,
            ],
            [
                'name' => 'Techista',
                'short_code' => 'TEC',
                'tags' => 'techos, filtraciones, impermeabilización',
                'code' => 'ACT014',
                'disabled' => false,
            ],
            [
                'name' => 'Reparador de electrodomésticos',
                'short_code' => 'ELEC',
                'tags' => 'heladeras, lavarropas, mantenimiento',
                'code' => 'ACT015',
                'disabled' => false,
            ],
            [
                'name' => 'Instalador de aires acondicionados',
                'short_code' => 'AIR',
                'tags' => 'climatización, aire, split',
                'code' => 'ACT016',
                'disabled' => false,
            ],
            [
                'name' => 'Mudanzas y fletes',
                'short_code' => 'MUD',
                'tags' => 'transporte, camión, carga',
                'code' => 'ACT017',
                'disabled' => false,
            ],
            [
                'name' => 'Fumigador',
                'short_code' => 'FUM',
                'tags' => 'plagas, desinfección, control',
                'code' => 'ACT018',
                'disabled' => false,
            ],
            [
                'name' => 'Herrero',
                'short_code' => 'HER',
                'tags' => 'puertas, rejas, estructuras',
                'code' => 'ACT019',
                'disabled' => false,
            ],
            [
                'name' => 'Cortador de césped',
                'short_code' => 'CES',
                'tags' => 'jardines, mantenimiento, pasto',
                'code' => 'ACT020',
                'disabled' => false,
            ],
        ];

        foreach ($activities as $activity) {
            DB::table('activities')->updateOrInsert(
                ['code' => $activity['code']],
                $activity
            );
        }
    }
}