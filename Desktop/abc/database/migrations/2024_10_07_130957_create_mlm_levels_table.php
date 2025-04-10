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
        Schema::create('mlm_levels', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name', 255);
        $table->integer('count');
        $table->double('commission', 10, 2);
        $table->double('cashback', 10, 2)->nullable();
        $table->tinyInteger('status')->default(0);
        $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mlm_levels');
    }
};
