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
        Schema::create('equipamentos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nome')->max(255);
            $table->string('descricao')->max(255);
            $table->string('serie')->unique()->max(255);
            $table->foreignId('status_id')->constrained('status')->references('id')->on('status');
            $table->string('imagem_url')->max(255);
            $table->foreignUuid('usuario_id')->constrained('users')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipamento');
    }
};
