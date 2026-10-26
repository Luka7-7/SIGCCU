<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AREAS = [
        'artes',
        'ciencias basicas e ingenierias',
        'ciencias economico y administrativas',
        'ciencias biologicas y agropecuarias',
        'ciencias de la salud',
        'ciencias sociales y humanidades',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('Carrera')) {
            Schema::create('Carrera', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nombre_carrera', 100);
                $table->string('unidad_academica', 100);
                $table->enum('area', self::AREAS);
                $table->string('institucion', 90);
            });
        }

        if (! Schema::hasTable('usuarios') || Schema::hasColumn('usuarios', 'Carrera_id')) {
            return;
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('Carrera_id')->nullable();
        });

        $hasInstitution = Schema::hasColumn('usuarios', 'institucion');
        $hasUnit = Schema::hasColumn('usuarios', 'unidad_academica');
        $hasProgram = Schema::hasColumn('usuarios', 'nombre_carrera');
        $hasArea = Schema::hasColumn('usuarios', 'area');

        $usuarios = DB::table('usuarios')->get();
        foreach ($usuarios as $usuario) {
            $area = $hasArea && in_array($usuario->area, self::AREAS, true)
                ? $usuario->area
                : 'ciencias sociales y humanidades';
            $carrera = [
                'nombre_carrera' => $hasProgram && $usuario->nombre_carrera
                    ? $usuario->nombre_carrera
                    : 'No especificada',
                'unidad_academica' => $hasUnit && $usuario->unidad_academica
                    ? $usuario->unidad_academica
                    : 'No especificada',
                'area' => $area,
                'institucion' => $hasInstitution && $usuario->institucion
                    ? $usuario->institucion
                    : 'Universidad Autónoma de Nayarit',
            ];

            $carreraId = DB::table('Carrera')->where($carrera)->value('id');
            if ($carreraId === null) {
                $carreraId = DB::table('Carrera')->insertGetId($carrera);
            }

            DB::table('usuarios')->where('id', $usuario->id)->update([
                'Carrera_id' => $carreraId,
            ]);
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('Carrera_id')->nullable(false)->change();
            $table->foreign('Carrera_id', 'fk_usuarios_Carrera1')
                ->references('id')
                ->on('Carrera')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        $legacyColumns = array_values(array_filter(
            ['institucion', 'unidad_academica', 'nombre_carrera', 'area'],
            fn (string $column): bool => Schema::hasColumn('usuarios', $column)
        ));

        if ($legacyColumns !== []) {
            Schema::table('usuarios', function (Blueprint $table) use ($legacyColumns) {
                $table->dropColumn($legacyColumns);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('usuarios') || ! Schema::hasColumn('usuarios', 'Carrera_id')) {
            Schema::dropIfExists('Carrera');

            return;
        }

        $legacyColumns = [
            'institucion' => fn (Blueprint $table) => $table->string('institucion', 90)->nullable(),
            'unidad_academica' => fn (Blueprint $table) => $table->string('unidad_academica', 100)->nullable(),
            'nombre_carrera' => fn (Blueprint $table) => $table->string('nombre_carrera', 100)->nullable(),
            'area' => fn (Blueprint $table) => $table->enum('area', self::AREAS)->nullable(),
        ];

        Schema::table('usuarios', function (Blueprint $table) use ($legacyColumns) {
            foreach ($legacyColumns as $column => $definition) {
                if (! Schema::hasColumn('usuarios', $column)) {
                    $definition($table);
                }
            }
        });

        if (Schema::hasTable('Carrera')) {
            DB::table('usuarios')
                ->join('Carrera', 'usuarios.Carrera_id', '=', 'Carrera.id')
                ->update([
                    'usuarios.institucion' => DB::raw('Carrera.institucion'),
                    'usuarios.unidad_academica' => DB::raw('Carrera.unidad_academica'),
                    'usuarios.nombre_carrera' => DB::raw('Carrera.nombre_carrera'),
                    'usuarios.area' => DB::raw('Carrera.area'),
                ]);
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign('fk_usuarios_Carrera1');
            $table->dropColumn('Carrera_id');
        });

        Schema::dropIfExists('Carrera');
    }
};
