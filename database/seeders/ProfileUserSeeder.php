<?php

namespace Database\Seeders;

use App\Models\ProfileUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $perfiles = [
            ['name' => 'Administrador'],
            ['name' => 'Prestadora'],
            ['name' => 'Cliente'],
        ];

        foreach ($perfiles as $perfil) {
            ProfileUser::firstOrCreate(['name' => $perfil['name']], $perfil);
        }
    }
}
