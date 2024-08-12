<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payments')->insert([
            'user_id' => 1,
            'amount' => 100,
            'status' => 'success',
        ]);
        DB::table('payments')->insert([
            'user_id' => 1,
            'amount' => 100,
            'status' => 'false',
        ]);
    }
}
