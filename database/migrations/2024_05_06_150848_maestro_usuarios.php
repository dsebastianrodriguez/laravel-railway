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
        Schema::create('mst_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->string('password');
            $table->string('nombre');
            $table->boolean('habilitado');
            $table->boolean('cambiar_password');
            $table->string('id_grupo');
            $table->string('idioma');
            $table->boolean('messenger');
            $table->boolean('administrable');
            $table->string('correo');
            $table->string('cedula');
            $table->timestamp('fecha_creacion');
            $table->timestamp('fecha_ineactivaccion');
            $table->timestamp('fecha_ultima_modificacion');
            $table->timestamp('fecha_ultimo_ingreso');
            $table->boolean('analisis_ventas');
            $table->string('telefono');
            $table->boolean('habilitar_requi');
            // $table->timestamps(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_usuarios');
    }
};
