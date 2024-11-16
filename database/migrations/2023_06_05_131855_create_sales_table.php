<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Negotiation - Negociación
        // Closed/Won - Cerrado/Ganado
        // Closed/Lost - Cerrado/Perdido
        // Follow-up - Seguimiento de la entrega
        // Pending - Pendiente de pago
        // In Progress - En Progreso para el envio
        // On Hold - En Espera de entrega
        // Cancelled - Cancelado/a
        // Archived - Archivado/a
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('(UUID())'));
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sale_user_id')->nullable();
            $table->enum('state', ['negotiation', 'follow_up', 'pending', 'in_progress', 'on_hold', 'closed_lost', 'closed_won', 'cancelled', 'archived']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('sale_user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
