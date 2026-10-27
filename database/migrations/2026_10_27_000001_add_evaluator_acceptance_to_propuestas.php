<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propuestas', function (Blueprint $table) {
            $table->boolean('evaluador_aceptado')->default(false);
        });

        DB::table('propuestas')
            ->whereNotNull('evaluador_id')
            ->update(['evaluador_aceptado' => true]);
    }

    public function down(): void
    {
        Schema::table('propuestas', function (Blueprint $table) {
            $table->dropColumn('evaluador_aceptado');
        });
    }
};
