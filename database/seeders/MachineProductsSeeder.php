<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade

class MachineProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // Get current timestamp

        $productItems = [
            [
                'id' => '01973a11-5e52-73b8-af3b-db8ad2024d75',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a03-b188-7289-92ba-f46b4533a520',
                'current_price' => 10.00,
                'quantity_in_stock' => 100,
                'slot_number' => 1,
            ],
            [
                'id' => '01973a11-9f4c-72e6-9bfc-5be4000b9f3e',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a04-4b75-70e3-9098-6fedd8fff323',
                'current_price' => 20.00,
                'quantity_in_stock' => 100,
                'slot_number' => 2,
            ],
            [
                'id' => '01973a11-ceee-72d1-b35d-0c477f47ded5',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a04-7d5a-73fc-81fe-b37c57edc88a',
                'current_price' => 30.00,
                'quantity_in_stock' => 100,
                'slot_number' => 3,
            ],
            [
                'id' => '01973a11-f0e9-707a-a0b2-9a07eb018cd5',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a04-c63c-7234-bda6-cd10e14f1559',
                'current_price' => 40.00,
                'quantity_in_stock' => 100,
                'slot_number' => 4,
            ],
            [
                'id' => '01973a12-10c5-7107-9df9-127d478e2389',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a04-e980-70be-8b6c-a3be37fc4f98',
                'current_price' => 50.00,
                'quantity_in_stock' => 100,
                'slot_number' => 5,
            ],
            [
                'id' => '01973a12-571b-70b0-9485-1bc68c3d3c26',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a05-1053-738f-bb84-d0b0ba83dbf9',
                'current_price' => 60.00,
                'quantity_in_stock' => 100,
                'slot_number' => 6,
            ],
            [
                'id' => '01973a12-72bc-73c8-a38e-fe6ec0563a24',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_products_id' => '01973a05-3fef-70e0-921c-4b6e6fec9f7d',
                'current_price' => 70.00,
                'quantity_in_stock' => 100,
                'slot_number' => 7,
            ],
        ];

        foreach ($productItems as $item) {
            DB::table('machine_products')
                ->insert(
                    [
                        'id' => $item['id'],
                        'vending_machine_id' => $item['vending_machine_id'],
                        'mas_products_id' => $item['mas_products_id'],
                        'current_price' => $item['current_price'],
                        'quantity_in_stock' => $item['quantity_in_stock'],
                        'slot_number' => $item['slot_number'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
        }
    }
}
