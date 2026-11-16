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
        Schema::table('equipament', function (Blueprint $table) {
            $table->unique('eqi_id');
        });

        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('historic_equipaments');

        Schema::create('historic_equipaments', function (Blueprint $table) {
            $table->id();
            $table->uuid('equipament_id');
            $table->foreign('equipament_id')->references('eqi_id')->on('equipament');
            $table->foreignId('status_id')->constrained('status');
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('historic_equipaments');

        Schema::create('historic_equipaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipament_id')->constrained('equipaments');
            $table->foreignId('status_id')->constrained('status');
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();

        Schema::table('equipament', function (Blueprint $table) {
            $table->dropUnique(['eqi_id']);
        });
    }
};
