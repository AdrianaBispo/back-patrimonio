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
        Schema::table('tipo_equipamentos', function (Blueprint $table) {
            if (Schema::hasColumn('tipo_equipamentos', 'name')) {
                $table->renameColumn('name', 'nome');
            }
        });

        Schema::table('tipo_equipamentos', function (Blueprint $table) {
            $table->string('descricao', 500)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_equipamentos', function (Blueprint $table) {
            $table->string('descricao', 150)->change();
        });

        Schema::table('tipo_equipamentos', function (Blueprint $table) {
            if (Schema::hasColumn('tipo_equipamentos', 'nome')) {
                $table->renameColumn('nome', 'name');
            }
        });
    }
};
