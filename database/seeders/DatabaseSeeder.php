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
        User::firstOrCreate(
            ['email' => 'admin@perfilesprofesionales.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('123456'),
                'phone' => '1122334455',
                'profile_user_id' => 1, // ⚠️ Ajustá según exista el perfil admin
                'locality_id' => 1, // ⚠️ Ajustá según exista el perfil admin
            ]
        );

        $this->call([
            CountriesSeeder::class,
            ProvincesSeeder::class,
            LocalitiesSeeder::class,
            ActivitiesSeeder::class,
            ProfileUserSeeder::class,
        ]);
    }
}
