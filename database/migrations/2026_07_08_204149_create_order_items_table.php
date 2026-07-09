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
        Schema::create('order_items', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // Relation avec la commande
            $table->uuid('order_id');

            // UUID du produit provenant du Product Service
            $table->uuid('product_id');

            // Quantité commandée
            $table->unsignedInteger('quantity');

            // Prix unitaire au moment de la commande
            $table->decimal('unit_price', 10, 2);

            // Sous-total = quantité × prix unitaire
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            // Clé étrangère uniquement vers la table orders
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};