<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Administrador', 'description' => 'Usuario con control total del sistema'],
            ['name' => 'Profesional', 'description' => 'Prestador de servicios o trabajador'],
            ['name' => 'Cliente', 'description' => 'Usuario que solicita servicios'],
        ];

        foreach ($types as $type) {
            UserType::updateOrCreate(
                ['name' => $type['name']], // condición única
                ['description' => $type['description']] // valores a actualizar
            );
        }
    }
}