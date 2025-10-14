<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['code' => 'DNI', 'name' => 'Documento Nacional de Identidad', 'description' => 'Documento argentino'],
            ['code' => 'PAS', 'name' => 'Pasaporte', 'description' => 'Pasaporte nacional o extranjero'],
            ['code' => 'CE',  'name' => 'Cédula de extranjería', 'description' => 'Documento para extranjeros residentes'],
        ];

        foreach ($types as $type) {
            DocumentType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}