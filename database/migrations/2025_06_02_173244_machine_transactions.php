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
        Schema::create(
            'machine_transactions',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('vending_machine_id')->constrained('vending_machines')->onDelete('restrict');
                $table->foreignUuid('machine_product_id')->constrained('machine_products')->onDelete('restrict'); // Product instance from machine
                $table->foreignUuid('user_id')->nullable()->constrained('user')->onDelete('set null'); // If user is deleted, transaction remains
                $table->integer('quantity_purchased')->default(1);
                $table->decimal('price_per_unit_at_transaction', 10, 2);
                $table->decimal('total_amount_due', 10, 2);
                $table->decimal('total_amount_paid', 10, 2)->default(0);
                $table->decimal('total_change_given', 10, 2)->default(0);
                $table->string('status')->comment(' Transaction status: pending, completed, failed, cancelled '); // 'pending', 'completed', 'failed', 'cancelled'
                $table->timestamp('transaction_at')->useCurrent();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_transactions');
    }
};
