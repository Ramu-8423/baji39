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
        Schema::create('virtual_games', function (Blueprint $table) {
            $table->id();
            $table->string('name',20);
            $table->integer('number');
            $table->integer('actual_number');
            $table->unsignedBigInteger('game_id');
            $table->double('multiplier',10,2);
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_games');
    }
};
