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
        Schema::create('catalogo_regimenes_fiscales', function (Blueprint $table) {
            $table->id();
            $table->string('num_regimen')->unique();
            $table->string('descripcion');
            $table->enum('tipo_persona', ['FISICA', 'MORAL']);
            $table->date('fecha_inicio_vigencia');
            $table->date('fecha_fin_vigencia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_regimenes_fiscales');
    }
};
