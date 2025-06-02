<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Str; // Import Str for UUID

class MasCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // Get current timestamp

        $cashItems = [
            // Coins
            ['value' => 1, 'type' => 'coin'],
            ['value' => 5, 'type' => 'coin'],
            ['value' => 10, 'type' => 'coin'],
            // Banknotes
            ['value' => 20, 'type' => 'bank_note'],
            ['value' => 50, 'type' => 'bank_note'],
            ['value' => 100, 'type' => 'bank_note'],
            ['value' => 500, 'type' => 'bank_note'],
            ['value' => 1000, 'type' => 'bank_note'],
        ];

        foreach ($cashItems as $item) {
            DB::table('mas_cash')
                ->insert(
                    [
                        'id' => Str::uuid()->toString(),
                        'value' => $item['value'],
                        'type' => $item['type'],
                        'currency' => 'THB',
                        'is_accepted' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
        }
    }
}
