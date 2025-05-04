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
        $table->float('energi');
        $table->timestamp('created_at')->useCurrent();
        $table->integer('week');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energis');
    }
};
