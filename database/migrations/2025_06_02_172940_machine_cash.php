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
            'machine_cash',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('vending_machine_id')->constrained('vending_machines')->onDelete('cascade');
                $table->foreignUuid('mas_cash_id')->constrained('mas_cash')->onDelete('restrict'); // Prevent deleting master cash if in use
                $table->integer('quantity')->default(0);
                $table->timestamps();

                $table->unique(['vending_machine_id', 'mas_cash_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_cash');
    }
};
