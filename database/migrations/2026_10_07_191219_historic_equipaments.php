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
        Schema::create('historic_equipaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipament_id')->constrained('equipaments')->references('id')->on('equipaments');
            $table->foreignId('status_id')->constrained('status')->references('id')->on('status');
            $table->foreignUuid('user_id')->constrained('users')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
