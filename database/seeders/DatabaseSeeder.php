<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        DB::table('users')->insert([
            ['id'=>1, 
            'username' => 'administrator',
            'role' => 'superadmin',
            'password' => bcrypt('123456'),
            'is_active' => 1]
        ]);

        $this->call([
            // MenuTableSeeder::class,
        ]);
    }
}
