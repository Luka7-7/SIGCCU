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
        // 1. Agregar columna Activo a asistencias si no existe
        if (Schema::hasTable('asistencias') && !Schema::hasColumn('asistencias', 'Activo')) {
            Schema::table('asistencias', function (Blueprint $table) {
                $table->boolean('Activo')->default(1)->after('ip');
            });
        }

        // 2. Agregar columna propuestascol a propuestas si no existe
        if (Schema::hasTable('propuestas') && !Schema::hasColumn('propuestas', 'propuestascol')) {
            Schema::table('propuestas', function (Blueprint $table) {
                $table->string('propuestascol', 45)->nullable()->after('evaluador_id');
            });
        }

        // 3. Crear tabla Comentarios_propuestas si no existe
        if (!Schema::hasTable('Comentarios_propuestas') && !Schema::hasTable('comentarios_propuestas')) {
            Schema::create('Comentarios_propuestas', function (Blueprint $table) {
                $table->integer('Id', true); // Auto-increment primary key
                $table->string('Comentario', 256);
                $table->unsignedInteger('propuestas_id');

                $table->foreign('propuestas_id', 'fk_Comentarios_propuestas_propuestas1')
                      ->references('id')
                      ->on('propuestas')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Comentarios_propuestas');

        if (Schema::hasTable('propuestas') && Schema::hasColumn('propuestas', 'propuestascol')) {
            Schema::table('propuestas', function (Blueprint $table) {
                $table->dropColumn('propuestascol');
            });
        }

        if (Schema::hasTable('asistencias') && Schema::hasColumn('asistencias', 'Activo')) {
            Schema::table('asistencias', function (Blueprint $table) {
                $table->dropColumn('Activo');
            });
        }
    }
};

