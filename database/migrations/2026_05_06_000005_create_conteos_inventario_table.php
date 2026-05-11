<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conteos_inventario', function (Blueprint $table) {
            $table->id('id_conteo');
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('detalles_conteo', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_conteo');
            $table->unsignedBigInteger('id_insumo');
            $table->decimal('stock_anterior', 10, 2);
            $table->decimal('stock_nuevo', 10, 2);
            $table->decimal('diferencia', 10, 2);
            $table->timestamps();

            $table->foreign('id_conteo')
                  ->references('id_conteo')->on('conteos_inventario')
                  ->onDelete('cascade');
            $table->foreign('id_insumo')
                  ->references('id_insumo')->on('insumos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_conteo');
        Schema::dropIfExists('conteos_inventario');
    }
};
