<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // รายการ mas_cash_id ใหม่ที่ต้องการใช้ (ตามลำดับ)
        $newMasCashIds = [
            '6c363f36-9526-4922-9757-5bdb2db46f5b',
            '362022c8-49b2-4486-b858-707b130aa1c3',
            '49bfa84a-12dc-4499-8312-14cc2d2edbbb',
            'f1245396-d810-4b46-8bc2-9a0e7295fc0c',
            'f92ae886-2f5b-44c4-853d-7d53365a00e1',
            '6096ba44-0505-4f90-b6d1-9826edc58625',
            '0fceed84-d87f-43d0-a9c1-ed7326272c2f',
            'bfc63fb0-32e9-4c6d-a3a8-e206aeaa558b',
        ];

        $machineCashItems = [
            [
                'id' => '01973a1b-52f5-71ed-a673-9ad9cff6229b',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[0], // อัปเดต ID แรก
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f6-7211-8147-37637bfdbc8e',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[1], // อัปเดต ID สอง
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f7-7174-a390-232ce9205013',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[2], // อัปเดต ID สาม
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f7-7174-a390-232cea0b900c',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[3], // อัปเดต ID สี่
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f8-7256-9ffe-2741cc354204',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[4], // อัปเดต ID ห้า
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f8-7256-9ffe-2741ccb8341d',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[5], // อัปเดต ID หก
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f9-71d5-b891-8aa79623ec2f',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[6], // อัปเดต ID เจ็ด
                'quantity' => 0,
            ],
            [
                'id' => '01973a1b-52f9-71d5-b891-8aa7970a0325',
                'vending_machine_id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'mas_cash_id' => $newMasCashIds[7], // อัปเดต ID แปด
                'quantity' => 0,
            ],
        ];

        foreach ($machineCashItems as $item) {
            DB::table('machine_cash')
                ->insert(
                    [
                        'id' => $item['id'],
                        'vending_machine_id' => $item['vending_machine_id'],
                        'mas_cash_id' => $item['mas_cash_id'],
                        'quantity' => $item['quantity'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
        }
    }
}
