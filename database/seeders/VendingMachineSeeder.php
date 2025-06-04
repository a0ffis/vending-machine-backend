<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Str; // Import Str for UUID

class VendingMachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now(); // Get current timestamp

        DB::table('vending_machines')->insert(
            [
                'id' => '019739fb-3bc8-7299-81c1-f847730fde85',
                'address' => '123 Main St, Bangkok, Thailand',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
