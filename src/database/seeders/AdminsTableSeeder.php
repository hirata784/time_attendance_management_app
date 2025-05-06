<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'テスト四郎',
            'email' => 'shiro@example.com',
            'password' => bcrypt('shiroshiro'),
        ];
        DB::table('admins')->insert($param);
        $param = [
            'name' => 'テスト五郎',
            'email' => 'goro@example.com',
            'password' => bcrypt('gorogoro'),
        ];
        DB::table('admins')->insert($param);
    }
}
