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
        Schema::create('energi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lampu_id')->constrained('lampu')->onDelete('cascade');
            $table->double('energi', 8, 2)->comment('Konsumsi daya dalam watt'); // Energi dalam watt
            $table->tinyInteger('kondisi')->comment('0:Mati, 1:Redup, 2:Sedang, 3:Terang'); // Untuk tracking kondisi lampu
            $table->integer('durasi')->default(1)->comment('Lama penggunaan dalam menit'); // Durasi dalam menit
            $table->integer('week');
            $table->integer('month');
            $table->integer('year');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energi');
    }
};
