<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Smalot\PdfParser\Parser;

class FacturaEmitida extends Model
{
    protected $fillable = [
        'cliente_id',
        'folio',
        'rfc_emisor',
        'nombre_emisor',
        'rfc_receptor',
        'nombre_receptor',
        'fecha_emision',
        'subtotal',
        'iva_trasladado',
        'iva_retenido',
        'isr_retenido',
        'total',
        'pdf_filename',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public static function procesarPDF(string $rutaArchivo): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($rutaArchivo);
        $texto = $pdf->getText();

        // Reemplazar saltos de línea con espacios
        $texto = preg_replace('/\s+/', ' ', $texto);

        $nombreArchivo = basename($rutaArchivo); // Obtén solo el nombre del archivo

        // Extraer datos del texto del PDF
        preg_match('/Folio fiscal:\s*([A-Z0-9\-]+)/', $texto, $folio_fiscal);
        preg_match('/RFC emisor:\s*(\w+)/', $texto, $rfc_emisor);
        preg_match('/Nombre emisor:\s*(.+?)(?:\s+RFC receptor|$)/', $texto, $nombre_emisor);
        preg_match('/RFC receptor:\s*(\w+)/', $texto, $rfc_receptor);
        preg_match('/Nombre receptor:\s*(.+?)(?:\s+Código postal|$)/', $texto, $nombre_receptor);
        preg_match('/Código postal, fecha y hora de\s+emisión:\s*\d{5}\s([\d\-:\s]+)/', $texto, $fecha_emision);
        preg_match('/Subtotal\s*\$\s*([\d,]+\.\d+)/', $texto, $subtotal);
        preg_match('/Impuestos trasladados IVA 16\.00%\s+\$\s*([\d,]+\.\d+)/', $texto, $iva_trasladado); // Ajustado
        preg_match('/IVA Retención\s*[\d,\.]+\s*Tasa\s*[\d\.]+%\s*([\d,]+\.\d{2})/', $texto, $iva_retenido); // Ajustado
        //preg_match('/ISR\s*\$\s*([\d,]+\.\d{2})/', $texto, $isr_retenido);
        preg_match('/Total\s*\$\s*([\d,]+\.\d+)/', $texto, $total);

        // Capturar valores de ISR
        preg_match('/ISR\s*\$[\s\d,\.]+\s*\$\s*([\d,]+\.\d{2})/', $texto, $matches);

        // Convertir los valores a números
        $subtotal = isset($subtotal[1]) ? str_replace(',', '', $subtotal[1]) : 0;
        $iva_trasladado = isset($iva_trasladado[1]) ? str_replace(',', '', $iva_trasladado[1]) : 0;
        $iva_retenido = isset($iva_retenido[1]) ? str_replace(',', '', $iva_retenido[1]) : 0;
        $isr_retenido = isset($matches[1]) ? str_replace(',', '', $matches[1]) : 0;

        return [
            'pdf_filename' => $nombreArchivo,
            'folio' => $folio_fiscal[1] ?? null,
            'rfc_emisor' => $rfc_emisor[1] ?? null,
            'nombre_emisor' => $nombre_emisor[1] ?? null,
            'rfc_receptor' => $rfc_receptor[1] ?? null,
            'nombre_receptor' => $nombre_receptor[1] ?? null,
            'fecha_emision' => isset($fecha_emision[1]) ? date('Y-m-d H:i:s', strtotime(trim($fecha_emision[1]))) : null,
            'subtotal' => $subtotal, // Subtotal ajustado
            'iva_trasladado' => $iva_trasladado,
            'iva_retenido' => $iva_retenido,
            'isr_retenido' => $isr_retenido,
            'total' => isset($total[1]) ? str_replace(',', '', $total[1]) : 0,
        ];
    }
}

