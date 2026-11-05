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
        if (Schema::hasColumn('categorias', 'notas')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->renameColumn('notas', 'nota');
            });
        }

        if (Schema::hasColumn('categorias', 'nota')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->string('nota', 500)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categorias', 'nota')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->string('nota', 500)->nullable(false)->change();
                $table->renameColumn('nota', 'notas');
            });
        }
    }
};
