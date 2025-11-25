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
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('restrict');
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('restrict');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->string('numero_ingreso', 20)->unique();
            $table->date('fecha_ingreso');
            $table->date('fecha_recepcion')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'recibida', 'cancelada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();

            // Índices
            $table->index('numero_ingreso');
            $table->index('estado');
            $table->index('fecha_ingreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
