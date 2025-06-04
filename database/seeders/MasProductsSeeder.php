<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade

class MasProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // Get current timestamp

        $productItems = [
            [
                'id' => '01973a03-b188-7289-92ba-f46b4533a520',
                'name' => 'โค้ก',
                'default_price' => 10.00,
                'image_url' => '/1749025010_52FrO4XzYE.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '01973a04-4b75-70e3-9098-6fedd8fff323',
                'name' => 'น้ำปลา',
                'default_price' => 20.00,
                'image_url' => '/1749025049_f63iylwpVm.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '01973a04-7d5a-73fc-81fe-b37c57edc88a',
                'name' => 'น้ำมัน',
                'default_price' => 30.00,
                'image_url' => '/1749025062_LFEu9AcfHO.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '01973a04-c63c-7234-bda6-cd10e14f1559',
                'name' => 'pepsi',
                'default_price' => 30.00,
                'image_url' => '/1749025080_iQ3AliCxwl.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '01973a04-e980-70be-8b6c-a3be37fc4f98',
                'name' => 'เปา',
                'default_price' => 40.00,
                'image_url' => '/1749025089_hMPCCRiPJN.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '01973a05-1053-738f-bb84-d0b0ba83dbf9',
                'name' => 'รสดี',
                'default_price' => 50.00,
                'image_url' => '/1749025099_xcw6NMNCtV.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => '01973a05-3fef-70e0-921c-4b6e6fec9f7d',
                'name' => 'ข้าว',
                'default_price' => 60.00,
                'image_url' => '/1749025112_BAvHJUtL83.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($productItems as $item) {
            DB::table('mas_products')
                ->insert(
                    [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'default_price' => $item['default_price'],
                        'image_url' => $item['image_url'],
                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],
                    ]
                );
        }
    }
}
