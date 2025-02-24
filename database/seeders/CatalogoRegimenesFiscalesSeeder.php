<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoRegimenesFiscalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regimenes = [
            ['num_regimen' => '601', 'descripcion' => 'General de Ley Personas Morales', 'tipo_persona' => 'MORAL', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '603', 'descripcion' => 'Personas Morales con Fines no Lucrativos', 'tipo_persona' => 'MORAL', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '605', 'descripcion' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '606', 'descripcion' => 'Arrendamiento', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '607', 'descripcion' => 'Régimen de Enajenación o Adquisición de Bienes', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '608', 'descripcion' => 'Demás ingresos', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '610', 'descripcion' => 'Residentes en el Extranjero sin Establecimiento Permanente en México', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '611', 'descripcion' => 'Ingresos por Dividendos (socios y accionistas)', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '612', 'descripcion' => 'Personas Físicas con Actividades Empresariales y Profesionales', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '614', 'descripcion' => 'Ingresos por intereses', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '615', 'descripcion' => 'Régimen de los ingresos por obtención de premios', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '616', 'descripcion' => 'Sin obligaciones fiscales', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '620', 'descripcion' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos', 'tipo_persona' => 'MORAL', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '621', 'descripcion' => 'Incorporación Fiscal', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '622', 'descripcion' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras', 'tipo_persona' => 'MORAL', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '623', 'descripcion' => 'Opcional para Grupos de Sociedades', 'tipo_persona' => 'MORAL', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '624', 'descripcion' => 'Coordinados', 'tipo_persona' => 'MORAL', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '625', 'descripcion' => 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
            ['num_regimen' => '626', 'descripcion' => 'Régimen Simplificado de Confianza', 'tipo_persona' => 'FISICA', 'fecha_inicio_vigencia' => '2022-01-01', 'fecha_fin_vigencia' => null],
        ];

        DB::table('catalogo_regimenes_fiscales')->insert($regimenes);
    }
}
