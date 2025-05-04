<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('lampu', function (Blueprint $table) {
        $table->id();
        $table->string('nama_lampu', 255);
        $table->string('lokasi', 100);
        $table->tinyInteger('status')->default(0);
        $table->integer('intensitas');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampus');
    }
};
