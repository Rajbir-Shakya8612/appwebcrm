<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full access to the system'],
            ['name' => 'Salesperson', 'slug' => 'salesperson', 'description' => 'Can edit and manage content'],
            ['name' => 'User', 'slug' => 'user', 'description' => 'Regular user with limited access'],
        ]);
    }
}
