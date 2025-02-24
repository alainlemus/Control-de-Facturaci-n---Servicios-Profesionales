<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaRecibidasTable extends Migration
{
    public function up()
    {
        Schema::create('factura_recibidas', function (Blueprint $table) {
            $table->id();
            $table->string('pdf_filename')->nullable();
            $table->string('folio')->nullable();
            $table->string('rfc_emisor')->nullable();
            $table->string('nombre_emisor')->nullable();
            $table->string('rfc_receptor')->nullable();
            $table->string('nombre_receptor')->nullable();
            $table->timestamp('fecha_emision')->nullable();
            $table->string('uso_cfdi')->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->decimal('descuento', 15, 2)->nullable()->default(0);
            $table->decimal('iva', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('factura_recibidas');
    }
}
