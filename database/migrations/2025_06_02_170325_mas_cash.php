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
            'mas_cash',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->integer('value')->comment('มูลค่าของเงิน (เช่น 1, 5, 10, 20), NOT NULL');
                $table->enum('type', ['coin', 'bank_note'])->comment('ชนิดของเงิน (เช่น \'coin\', \'bank_note\'), NOT NULL');
                $table->string('currency')->default('THB')->comment('สกุลเงิน (เช่น \'THB\'), NOT NULL');
                $table->boolean('is_accepted')->default(true)->comment('สถานะว่ายังรับเงินชนิดนี้หรือไม่, NOT NULL');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mas_cash');
    }
};
