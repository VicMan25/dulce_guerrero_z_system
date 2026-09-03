<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lista ordenada de personas que participan en la rotación de aseo
        Schema::create('turnos_aseo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Historial de aseos realizados
        Schema::create('registros_aseo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_usuario');
            $table->date('fecha');
            $table->string('motivo')->default('turno'); // turno | llegada_tarde
            $table->unsignedInteger('ciclo')->default(1);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_aseo');
        Schema::dropIfExists('turnos_aseo');
    }
};
