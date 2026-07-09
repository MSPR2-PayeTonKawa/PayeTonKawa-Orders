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
    Schema::create('orders', function (Blueprint $table) {

        $table->uuid('id')->primary();

        // Référence au client (micro-service Customers)
        $table->uuid('customer_id');

        // Montant total de la commande
        $table->decimal('total_amount', 10, 2)->default(0);

        // Statut de la commande
        $table->enum('status', [
            'pending',
            'paid',
            'cancelled'
        ])->default('pending');

        // Statut du paiement
        $table->enum('payment_status', [
            'pending',
            'paid',
            'failed'
        ])->default('pending');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
