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
        Schema::create('seguimiento_clinico', function (Blueprint $table) {
            $table->id('id_seguimiento');
            $table->foreignId('id_cita')->constrained('citas', 'id_cita')->onDelete('cascade');
            $table->text('observaciones');
            $table->text('tratamiento');
            $table->date('fecha_seguimiento');
            $table->date('proxima_revision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimiento_clinico');
    }
};
