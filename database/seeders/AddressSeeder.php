<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table_name = 'addresses';

        DB::table($table_name)->insert([
            'user_id' => 1,
            'recipient_name' => 'Admin',
            'recipient_phone' => '08123456789',
            'address_line' => 'Jl. RA. Kartini, Jombang Wetan, Kec. Jombang, Kota Cilegon, Banten 42411',
            'city' => 'Cilegon'
        ]);
        DB::table($table_name)->insert([
            'user_id' => 2,
            'address_label' => 'Rumah saya',
            'recipient_name' => 'Big Jack',
            'recipient_phone' => '08123456789',
            'address_line' => 'Jl. Kesehatan XI No.13, RT.1/RW.6, Petojo Sel., Kecamatan Gambir, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10160',
            'district' => 'Gambir',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10160',
            'is_default' => 1
        ]);
        DB::table($table_name)->insert([
            'user_id' => 2,
            'recipient_name' => 'Little Jack',
            'recipient_phone' => '08123456789',
            'address_line' => 'Jl. Puri Kresna No.31, Kotasari, Kec. Gerogol, Kota Cilegon, Banten 42436',
            'city' => 'Cilegon'
        ]);
    }
}
