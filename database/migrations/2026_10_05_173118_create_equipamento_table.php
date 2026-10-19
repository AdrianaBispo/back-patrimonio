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
        Schema::create('equipament', function (Blueprint $table) {
            $table->uuid('eqi_id');
            $table->timestamps();
            $table->string('eqi_name')->max(255);
            $table->string('eqi_descricao')->max(255);
            $table->string('eqi_serie')->unique()->max(255);
            $table->foreignId('eqi_status_id')->constrained('status')->references('id')->on('status');
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
