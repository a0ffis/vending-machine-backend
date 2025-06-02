<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {

        Schema::create(
            'vending_machines',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('address')->nullable()->comment('ที่ตั้งของเครื่อง (Nullable)');
                $table->enum(
                    'status',
                    ['active', 'maintenance', 'out_of_service']
                )->default('active')->nullable(false)->comment('สถานะเครื่อง (เช่น \'active\', \'maintenance\', \'out_of_service\'), NOT NULL');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('vending_machines');
    }
};
