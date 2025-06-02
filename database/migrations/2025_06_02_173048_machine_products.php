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
            'machine_products',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('vending_machine_id')->constrained('vending_machines')->onDelete('cascade');
                $table->foreignUuid('mas_products_id')->constrained('mas_products')->onDelete('restrict'); // Prevent deleting master product if in use
                $table->decimal('current_price', 10, 2);
                $table->integer('quantity_in_stock')->default(0);
                $table->string('slot_number')->nullable(); // Can be null if product isn't in a specific slot or not needed
                $table->timestamps();

                // Consider unique constraints carefully based on your logic
                // Option 1: A product type can only be in one slot per machine (if slot_number is always used)
                // $table->unique(['vending_machine_id', 'mas_products_id', 'slot_number']);
                // Option 2: A slot can only hold one type of product
                // $table->unique(['vending_machine_id', 'slot_number']);
                // Option 3: A product type can only appear once per machine (even if in different "conceptual" slots not tracked by slot_number)
                // $table->unique(['vending_machine_id', 'mas_products_id']);
                // For now, let's assume a product appears once per machine (identified by mas_products_id)
                // If multiple slots can have the same product, this table structure might need refinement, or this constraint removed.
                $table->unique(['vending_machine_id', 'mas_products_id'], 'machine_product_unique');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_products');
    }
};
