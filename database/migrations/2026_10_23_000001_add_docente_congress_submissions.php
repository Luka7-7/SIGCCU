<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('congresos', function (Blueprint $table) {
            $table->unsignedInteger('solicitante_id')->nullable();
            $table->string('solicitud_pdf', 255)->nullable();
            $table->dateTime('solicitada_en')->nullable();

            $table->foreign('solicitante_id', 'fk_congresos_solicitante')
                ->references('id')->on('usuarios')
                ->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::create('congresos_programas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('congreso_id');
            $table->string('programa', 100);

            $table->unique(['congreso_id', 'programa'], 'congresos_programas_unique');
            $table->foreign('congreso_id', 'fk_congresos_programas_congreso')
                ->references('id')->on('congresos')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('congresos_programas');

        Schema::table('congresos', function (Blueprint $table) {
            $table->dropForeign('fk_congresos_solicitante');
            $table->dropColumn(['solicitante_id', 'solicitud_pdf', 'solicitada_en']);
        });
    }
};
