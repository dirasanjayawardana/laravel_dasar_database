<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeding (Melakukan perubahan (insert, update, delete) data di database)
        DB::table("counters")->insert([
            "id" => "sample",
            "counter" => 0
        ]);
    }
}
