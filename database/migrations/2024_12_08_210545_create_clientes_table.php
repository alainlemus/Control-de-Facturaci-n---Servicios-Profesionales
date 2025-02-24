<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('rfc')->unique();
            $table->string('nombre_cliente');
            $table->string('razon_social');
            $table->foreignId('catalogo_regimen_fiscal_id')->constrained('catalogo_regimenes_fiscales')->onDelete('cascade')->after('id');;
            $table->enum('tipo_persona', ['FISICA', 'MORAL']); // Campo régimen
            $table->string('codigo_postal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
