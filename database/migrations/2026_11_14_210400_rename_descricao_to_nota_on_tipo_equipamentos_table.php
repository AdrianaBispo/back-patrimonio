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
        if (Schema::hasColumn('tipo_equipamentos', 'descricao')) {
            Schema::table('tipo_equipamentos', function (Blueprint $table) {
                $table->renameColumn('descricao', 'nota');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tipo_equipamentos', 'nota')) {
            Schema::table('tipo_equipamentos', function (Blueprint $table) {
                $table->renameColumn('nota', 'descricao');
            });
        }
    }
};
