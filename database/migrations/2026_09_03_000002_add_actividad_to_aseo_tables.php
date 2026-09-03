<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos_aseo', function (Blueprint $table) {
            $table->string('actividad')->default('banos')->after('id');
        });

        Schema::table('turnos_aseo', function (Blueprint $table) {
            // La FK de user_id se apoya en el índice único; hay que soltarla antes de cambiarlo.
            $table->dropForeign(['user_id']);
            $table->dropUnique('turnos_aseo_user_id_unique');

            // Una persona puede estar en la lista de cada actividad (baños, canecas, …).
            $table->unique(['actividad', 'user_id']);
            // Índice que necesita la FK (el único compuesto empieza por "actividad").
            $table->index('user_id', 'turnos_aseo_user_id_index');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('registros_aseo', function (Blueprint $table) {
            $table->string('actividad')->default('banos')->after('id');
            $table->string('nota')->nullable()->after('motivo');
        });
    }

    public function down(): void
    {
        Schema::table('registros_aseo', function (Blueprint $table) {
            $table->dropColumn(['actividad', 'nota']);
        });

        Schema::table('turnos_aseo', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('turnos_aseo_user_id_index');
            $table->dropUnique(['actividad', 'user_id']);

            $table->unique('user_id', 'turnos_aseo_user_id_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dropColumn('actividad');
        });
    }
};
