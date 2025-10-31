<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\User;
use Faker\Factory as Faker;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_AR');

        // Obtener solo usuarios profesionales
        $users = User::where('user_type_id', 2)->get();

        foreach ($users as $user) {
            // Crear entre 1 y 5 preguntas por profesional
            for ($i = 0; $i < rand(1, 5); $i++) {
                Question::updateOrCreate(
                    [
                        'email' => $faker->unique()->safeEmail,
                        'user_id' => $user->id,
                    ],
                    [
                        'name' => $faker->name,
                        'message' => $faker->sentence(rand(8, 15)), // pregunta simulada
                        'answer' => $faker->boolean(40) ? $faker->sentence(rand(6, 12)) : null, // posible respuesta
                        'published' => $faker->boolean(85),
                    ]
                );
            }
        }
    }
}