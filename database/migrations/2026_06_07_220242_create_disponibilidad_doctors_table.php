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
        Schema::create('disponibilidades_doctores', function (Blueprint $table) {
            $table->id('id_disponibilidad');
            $table->foreignId('id_doctor')->constrained('doctores', 'id_doctor')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora');
            $table->boolean('reservado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilidades_doctores');
    }
};
