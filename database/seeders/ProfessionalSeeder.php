<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_AR'); // Faker con nombres argentinos

        // Obtener las actividades existentes
        $activities = Activity::all();

        if ($activities->isEmpty()) {
            $this->command->warn('⚠️ No hay actividades registradas. Ejecutá el seeder de actividades primero.');
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            $user = User::updateOrCreate(
                [
                    'email' => $faker->unique()->safeEmail,
                ],
                [
                    'username' => $faker->userName,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password123'), // Contraseña por defecto
                    'document_type' => 'DNI',
                    'document_number' => $faker->unique()->numerify('########'),
                    'birth_date' => $faker->date('Y-m-d', '2000-01-01'),
                    'nationality' => 'Argentina',
                    'country_phone' => '+54',
                    'area_code' => $faker->numerify('11'),
                    'phone_number' => $faker->numerify('########'),
                    'profile_picture' => null,
                    'description' => $faker->sentence(8),
                    'user_type_id' => 2, // 2 = Profesional
                    'locality_id' => rand(1, 20), // Ajustar según tus datos
                    'address' => $faker->streetAddress,
                    'street' => $faker->streetName,
                    'street_number' => $faker->buildingNumber,
                    'floor' => rand(0, 10),
                    'apartment' => $faker->randomElement(['A', 'B', 'C', null]),
                ]
            );

            // Asociar entre 1 y 3 actividades aleatorias
            $randomActivities = $activities->random(rand(1, 3))->pluck('id')->toArray();
            $user->activities()->syncWithoutDetaching($randomActivities);
        }

        $this->command->info('✅ Se crearon 50 profesionales con actividades asociadas.');
    }
}
