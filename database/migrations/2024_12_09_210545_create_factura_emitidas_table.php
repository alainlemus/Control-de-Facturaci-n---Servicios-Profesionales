<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('factura_emitidas', function (Blueprint $table) {
            $table->id(); // Llave primaria
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade')->after('id');
            $table->string('folio')->nullable(); // Folio puede ser nulo
            $table->string('rfc_emisor'); // RFC del emisor
            $table->string('nombre_emisor'); // Nombre del emisor
            $table->string('rfc_receptor'); // RFC del receptor
            $table->string('nombre_receptor'); // Nombre del receptor
            $table->dateTime('fecha_emision')->nullable(); // Fecha de emisión de la factura
            $table->decimal('subtotal', 15, 2)->default(0); // Subtotal de la factura
            $table->decimal('iva_trasladado', 15, 2)->default(0); // IVA trasladado
            $table->decimal('iva_retenido', 15, 2)->default(0); // IVA retenido
            $table->decimal('isr_retenido', 15, 2)->default(0); // ISR retenido
            $table->decimal('total', 15, 2)->default(0); // Total de la factura
            $table->string('pdf_filename')->nullable(); // Nombre del archivo PDF asociado
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_emitidas');
    }
};
