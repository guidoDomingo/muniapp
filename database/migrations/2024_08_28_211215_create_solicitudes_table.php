<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesTable extends Migration
{
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tramite_id')->constrained('tramites')->onDelete('cascade');
            $table->json('formulario')->nullable(); // Columna JSON para almacenar el formulario completo
            $table->string('detalles');
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('solicitudes');
    }
}


