<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->tinyInteger('featured_position')
                ->nullable()
                ->comment('1 o 2, define su lugar en la página principal');
        });

        // Solo permite 1 o 2 distintos no nulos
        DB::statement("
            CREATE UNIQUE INDEX workshops_featured_position_unique
            ON workshops (featured_position)
            WHERE featured_position IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('featured_position');
        });

        DB::statement("DROP INDEX IF EXISTS workshops_featured_position_unique");
    }
};
