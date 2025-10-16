<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_AR');

        $users = User::where('user_type_id', 2)->get(); // solo profesionales

        foreach ($users as $user) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                Review::updateOrCreate(
                    [
                        'email' => $faker->unique()->safeEmail,
                        'user_id' => $user->id,
                    ],
                    [
                        'name' => $faker->name,
                        'value' => rand(1, 5),
                        'comment' => $faker->boolean(70) ? $faker->sentence(10) : null,
                        'answer' => $faker->boolean(30) ? $faker->sentence(8) : null,
                        'published' => $faker->boolean(80),
                    ]
                );
            }
        }
    }
}
