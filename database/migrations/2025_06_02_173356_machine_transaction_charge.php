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
            'machine_transaction_charge',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('machine_transaction_id')->constrained('machine_transactions')->onDelete('cascade');
                $table->foreignUuid('mas_cash_id')->constrained('mas_cash')->onDelete('restrict'); // Type of cash given as change
                $table->integer('quantity'); // How many of this coin/note
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_transaction_charge');
    }
};
