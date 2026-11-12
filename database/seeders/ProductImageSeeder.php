<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table_name = 'product_images';
        DB::table($table_name)->insert([
            'product_id' => 1,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSlV-h4ov8wVxlEH4GpVIXorUNHbxm9IODl82d2G3O6I6ON9-siSj2C0k0&s=10',
            'is_primary' => 1,
            'sort_order' => 0
        ]);
        DB::table($table_name)->insert([
            'product_id' => 1,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSyis46JIZQO2HaZah7QfThF7unlwdTQWdnA__WEpT-a0JsgvRRXEvQNb8&s=10',
            'is_primary' => 0,
            'sort_order' => 1
        ]);
        DB::table($table_name)->insert([
            'product_id' => 2,
            'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/0/03/PalmPilot_Professional.jpg?utm_source=commons.wikimedia.org&utm_campaign=index&utm_content=thumbnail_unscaled&_=20100322010245',
        ]);
    }
}
