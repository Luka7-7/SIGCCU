<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('correo_electronico', 100);
            $table->string('contrasena', 255);
            $table->string('nombres', 70);
            $table->string('apellidos', 80);
            $table->enum('rol', ['administrador', 'alumno', 'docente', 'externo']);
            $table->string('institucion', 90);
            $table->string('matricula_numtrabajador', 8)->nullable();
            $table->string('unidad_academica', 100)->nullable();
            $table->string('nombre_carrera', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->dateTime('creado_en')->useCurrent();
            $table->enum('area', [
                'artes',
                'ciencias basicas e ingenierias',
                'ciencias economico y administrativas',
                'ciencias biologicas y agropecuarias',
                'ciencias de la salud',
                'ciencias sociales y humanidades',
            ]);

            $table->unique('correo_electronico', 'correo_electronico_UNIQUE');
        });

        Schema::create('congresos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 120);
            $table->string('siglas', 12);
            $table->integer('edicion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->dateTime('registro_abre');
            $table->dateTime('registro_cierre');
            $table->text('descripcion');
            $table->string('logo_url', 255)->nullable();
            $table->integer('minimo_talleres')->default(0);
            $table->integer('minimo_conferencias')->default(0);
            $table->string('eje_tematico', 100);
            $table->enum('dictamen', ['aceptada', 'con observaciones', 'rechazada'])->nullable();
        });

        Schema::create('sedes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->string('direccion', 150);
            $table->string('ciudad', 60);
            $table->string('estado', 60);
            $table->string('url_ubicacion', 255);
        });

        Schema::create('congresos_sedes', function (Blueprint $table) {
            $table->unsignedInteger('congreso_id');
            $table->unsignedInteger('sede_id');

            $table->primary(['congreso_id', 'sede_id']);

            $table->foreign('congreso_id', 'fk_congresos_sedes_congresos')
                ->references('id')->on('congresos')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sede_id', 'fk_congresos_sedes_sedes')
                ->references('id')->on('sedes')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('conferencias_talleres', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descripcion');
            $table->enum('tipo', ['conferencia', 'taller']);
            $table->integer('cupo');
            $table->string('ubicacion', 100);
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedInteger('congreso_id');
            $table->string('qr_link', 50)->nullable();

            $table->foreign('congreso_id', 'fk_conferencias_talleres_congresos')
                ->references('id')->on('congresos')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('inscripciones_congresos', function (Blueprint $table) {
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('congreso_id');
            $table->dateTime('fecha_hora_inscripcion')->useCurrent();
            $table->boolean('aceptado')->default(false);
            $table->dateTime('fecha_hora_respuesta')->nullable();
            $table->enum('rol_congreso', ['organizador', 'comite', 'asistente y/o ponente'])->nullable();

            $table->primary(['usuario_id', 'congreso_id']);

            $table->foreign('usuario_id', 'fk_inscripciones_congresos_usuarios')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('congreso_id', 'fk_inscripciones_congresos_congresos')
                ->references('id')->on('congresos')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('inscripciones_conferencias_talleres', function (Blueprint $table) {
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('conferencia_taller_id');
            $table->dateTime('fecha_hora_inscripcion')->useCurrent();
            $table->boolean('aceptado')->default(false);
            $table->dateTime('fecha_hora_respuesta')->nullable();
            $table->enum('tipo_participacion', ['asistente', 'ponente', 'tallerista'])->nullable();

            $table->primary(['usuario_id', 'conferencia_taller_id']);

            $table->foreign('usuario_id', 'fk_inscripciones_ct_usuarios')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('conferencia_taller_id', 'fk_inscripciones_ct_conferencias_talleres')
                ->references('id')->on('conferencias_talleres')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('asistencias', function (Blueprint $table) {
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('conferencia_taller_id');
            $table->dateTime('fecha_hora')->useCurrent();
            $table->string('ip', 45);

            $table->primary(['usuario_id', 'conferencia_taller_id']);

            $table->foreign(['usuario_id', 'conferencia_taller_id'], 'fk_asistencias_inscripciones')
                ->references(['usuario_id', 'conferencia_taller_id'])
                ->on('inscripciones_conferencias_talleres')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('constancias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('congreso_id')->nullable();
            $table->unsignedInteger('conferencia_taller_id')->nullable();
            $table->string('url_constancia', 255);

            $table->foreign('congreso_id', 'fk_constancias_congresos')
                ->references('id')->on('congresos')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('conferencia_taller_id', 'fk_constancias_conferencias_talleres')
                ->references('id')->on('conferencias_talleres')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('usuario_id', 'fk_constancias_usuarios')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('evaluadores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->string('licenciatura', 100);
            $table->string('maestria', 100)->nullable();
            $table->string('doctorado', 100)->nullable();

            $table->unique('usuario_id', 'usuario_id_UNIQUE');

            $table->foreign('usuario_id', 'fk_evaluadores_usuarios')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('propuestas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('resumen_url', 255);
            $table->string('extenso_url', 255)->nullable();
            $table->enum('resumen_estado', ['Enviado', 'En revision', 'A corregir', 'Aceptado', 'Rechazado'])
                ->default('Enviado');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('congreso_id');
            $table->unsignedInteger('evaluador_id')->nullable();

            $table->foreign('usuario_id', 'fk_propuestas_usuarios')
                ->references('id')->on('usuarios')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('congreso_id', 'fk_propuestas_congresos')
                ->references('id')->on('congresos')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('evaluador_id', 'fk_propuestas_evaluadores')
                ->references('id')->on('evaluadores')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propuestas');
        Schema::dropIfExists('evaluadores');
        Schema::dropIfExists('constancias');
        Schema::dropIfExists('asistencias');
        Schema::dropIfExists('inscripciones_conferencias_talleres');
        Schema::dropIfExists('inscripciones_congresos');
        Schema::dropIfExists('conferencias_talleres');
        Schema::dropIfExists('congresos_sedes');
        Schema::dropIfExists('sedes');
        Schema::dropIfExists('congresos');
        Schema::dropIfExists('usuarios');
    }
};