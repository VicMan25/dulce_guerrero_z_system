<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_receta', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_receta');
            $table->unsignedBigInteger('id_insumo');
            $table->decimal('cantidad', 10, 2);

            $table->foreign('id_receta')
                ->references('id_receta')
                ->on('recetas')
                ->onDelete('cascade');

            $table->foreign('id_insumo')
                ->references('id_insumo')
                ->on('insumos')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_receta');
    }
};