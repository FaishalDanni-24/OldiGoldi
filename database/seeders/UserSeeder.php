<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'role_id' => 0,
            'name' => 'Admin',
            'email' => 'admin1'.'@example.com',
            'email_verified_at' => date('Y-m-d H:i:s', time()),
            'password' => Hash::make('password'),
            'phone_number' => '081234567890',
            'account_status' => 'active',
            'remember_token' => Str::random(10)
            ]);
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => 'User',
            'email' => 'user1'.'@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
