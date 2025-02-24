<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfc',
        'nombre_cliente',
        'razon_social',
        'catalogo_regimen_fiscal_id',
        'tipo_persona',
        'codigo_postal',
    ];

    public function catalogoRegimenFiscal()
    {
        return $this->belongsTo(CatalogoRegimenFiscal::class);
    }
}
