<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Smalot\PdfParser\Parser;

class FacturaRecibida extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdf_filename',
        'folio',
        'rfc_emisor',
        'nombre_emisor',
        'rfc_receptor',
        'nombre_receptor',
        'fecha_emision',
        'uso_cfdi',
        'subtotal',
        'iva',
        'total',
    ];

    public static function procesarPDF(string $rutaArchivo)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($rutaArchivo);
        $texto = $pdf->getText();

        $nombreArchivo = basename($rutaArchivo); // Obtén solo el nombre del archivo

        // Ajuste de expresiones regulares
        preg_match('/Folio fiscal:\s*([A-Z0-9\-]+)/', $texto, $folio_fiscal);
        preg_match('/RFC emisor:\s*(\w+)/', $texto, $rfc_emisor);
        preg_match('/Nombre emisor:\s*(.+)/', $texto, $nombre_emisor);
        preg_match('/RFC receptor:\s*(\w+)/', $texto, $rfc_receptor);
        preg_match('/Nombre receptor:\s*(.+?)(?:\s+Código postal|$)/', $texto, $nombre_receptor);
        preg_match('/Código postal, fecha y hora de\s+emisión:\s*\d{5}\s([\d\-:\s]+)/', $texto, $fecha_emision);
        preg_match('/Uso CFDI:\s*(.+)/', $texto, $uso_cfdi);
        preg_match('/Subtotal\s*\$\s*([\d,]+\.\d{2})/', $texto, $subtotal);
        preg_match('/Descuento\s*\$\s*([\d,]+\.\d+)/', $texto, $descuento);
        preg_match('/Impuestos trasladados IVA 16\.00%\s*\$\s*([\d,]+\.\d{2})/', $texto, $iva);
        preg_match('/Total\s*\$\s*([\d,]+\.\d{2})/', $texto, $total);

        // Convertir los valores a números
        $subtotal = isset($subtotal[1]) ? str_replace(',', '', $subtotal[1]) : 0;

        if($descuento != 0){

            $descuento = isset($descuento[1]) ? str_replace(',', '', $descuento[1]) : 0;
            // Calcular el subtotal ajustado
            $subtotalFinal = $subtotal - $descuento;

        }else{
            $subtotalFinal = $subtotal;
        }

        return [
            'pdf_filename' => $nombreArchivo,
            'folio' => $folio[1] ?? null,
            'rfc_emisor' => $rfc_emisor[1] ?? null,
            'nombre_emisor' => $nombre_emisor[1] ?? null,
            'rfc_receptor' => $rfc_receptor[1] ?? null,
            'nombre_receptor' => $nombre_receptor[1] ?? null,
            'fecha_emision' => isset($fecha_emision[1]) ? date('Y-m-d H:i:s', strtotime(trim($fecha_emision[1]))) : null,
            'uso_cfdi' => $uso_cfdi[1] ?? null,
            'subtotal' => $subtotalFinal,
            'descuento' => $descuento,
            'iva' => isset($iva[1]) ? str_replace(',', '', $iva[1]) : 0,
            'total' => isset($total[1]) ? str_replace(',', '', $total[1]) : 0,
        ];
    }
}
