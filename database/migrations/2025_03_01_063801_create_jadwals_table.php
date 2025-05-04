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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lampu_id')->constrained('lampu')->onDelete('cascade');
            $table->string('hari', 50);
            $table->time('waktu_nyala');
            $table->time('waktu_mati');
            $table->string('frekuensi')->default('once')->comment('once, daily, weekly, monthly');
            $table->integer('tanggal_bulanan')->nullable()->comment('Tanggal untuk jadwal bulanan');
            $table->integer('intensitas')->default(100)->comment('Intensitas cahaya lampu (0-100)');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
