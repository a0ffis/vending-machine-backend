<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
// ไม่จำเป็นต้องใช้ Str::uuid() อีกต่อไป ถ้า ID ถูกระบุมาแล้ว
// use Illuminate\Support\Str;

class MasCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // Get current timestamp

        $cashItemsData = [
            [
                'id' => '6c363f36-9526-4922-9757-5bdb2db46f5b',
                'value' => 1,
                'type' => 'coin',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => '362022c8-49b2-4486-b858-707b130aa1c3',
                'value' => 5,
                'type' => 'coin',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => '49bfa84a-12dc-4499-8312-14cc2d2edbbb',
                'value' => 10,
                'type' => 'coin',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => 'f1245396-d810-4b46-8bc2-9a0e7295fc0c',
                'value' => 20,
                'type' => 'bank_note',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => 'f92ae886-2f5b-44c4-853d-7d53365a00e1',
                'value' => 50,
                'type' => 'bank_note',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => '6096ba44-0505-4f90-b6d1-9826edc58625',
                'value' => 100,
                'type' => 'bank_note',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => '0fceed84-d87f-43d0-a9c1-ed7326272c2f',
                'value' => 500,
                'type' => 'bank_note',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
            [
                'id' => 'bfc63fb0-32e9-4c6d-a3a8-e206aeaa558b',
                'value' => 1000,
                'type' => 'bank_note',
                'currency' => 'THB',
                'is_accepted' => true,
            ],
        ];

        foreach ($cashItemsData as $item) {
            DB::table('mas_cash')
                ->insert(
                    [
                        'id' => $item['id'], // ใช้ ID ที่ระบุมา
                        'value' => $item['value'],
                        'type' => $item['type'],
                        'currency' => $item['currency'],
                        'is_accepted' => $item['is_accepted'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
        }
    }
}
