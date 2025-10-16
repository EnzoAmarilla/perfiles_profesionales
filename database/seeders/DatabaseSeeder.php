<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@perfilesprofesionales.com'],
            [
                // Datos básicos
                'username' => 'admin',
                'first_name' => 'Administrador',
                'last_name' => 'General',
                'name' => 'Administrador General',
                
                // Documento
                'document_type' => 'DNI',
                'document_number' => '12345678',
                
                // Fecha de nacimiento y nacionalidad
                'birth_date' => '1990-01-01',
                'nationality' => 'Argentina',

                // Contacto
                'country_phone' => '+54',
                'area_code' => '11',
                'phone_number' => '22334455',

                // Ubicación
                'locality_id' => 1,
                'address' => 'Domicilio Administrador',
                'street' => 'Av. Principal',
                'street_number' => '1000',
                'floor' => '1',
                'apartment' => 'A',

                // Perfil
                'description' => 'Usuario con control total del sistema',
                'profile_picture' => null,

                // Tipo de usuario (Administrador)
                'user_type_id' => 1,

                // Credenciales
                'password' => Hash::make('123456'),
            ]
        );

        $this->call([
            ActivitiesSeeder::class,
            UserTypeSeeder::class,
            DocumentTypeSeeder::class,
            ProfessionalSeeder::class,
        ]);
    }
}
