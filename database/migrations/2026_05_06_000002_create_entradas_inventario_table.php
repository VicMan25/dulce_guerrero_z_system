<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas_inventario', function (Blueprint $table) {
            $table->id('id_entrada');
            $table->foreignId('id_insumo')->constrained('insumos', 'id_insumo');
            $table->decimal('cantidad', 10, 2);
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas_inventario');
    }
};
