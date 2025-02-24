<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::create([
            'rfc' => 'FID741230A22',
            'nombre_cliente' => 'INFOTEC',
            'razon_social' => 'INFOTEC CENTRO DE INVESTIGACION E INNOVACION EN TECNOLOGIAS DE LA INFORMACION Y COMUNICACION',
            'catalogo_regimen_fiscal_id' => 2, // Asegúrate de que este ID exista en la tabla catalogo_regimenes_fiscales
            'tipo_persona' => 'MORAL',
            'codigo_postal' => '14050',
        ]);
    }
}
