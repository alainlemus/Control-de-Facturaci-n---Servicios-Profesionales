<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoRegimenFiscal extends Model
{
    use HasFactory;

    protected $table = 'catalogo_regimenes_fiscales';

    protected $fillable = [
        'num_regimen',
        'descripcion',
        'tipo_persona',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia',
    ];
}
