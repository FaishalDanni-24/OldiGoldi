<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'category_name' => 'PDA',
            'slug' => 'pda',
            'description' => 'Personal Digital Assistant untuk kebutuhan pribadi seperti kalendar, komunikasi kontak, serta buat list dan catatan.',
            'is_active' => 0,
        ]);
        DB::table('categories')->insert([
            'category_name' => 'Laptop Mid Range',
            'slug' => 'laptop-mid-range',
            'description' => 'Laptop untuk beban harian, kuat untuk kebutuhan kantor/kuliah biasa.',
        ]);
    }
}
