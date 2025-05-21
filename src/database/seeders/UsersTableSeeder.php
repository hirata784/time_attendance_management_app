<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'テスト一郎',
            'email' => 'ichiro@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('ichiichi'),
        ];
        DB::table('users')->insert($param);
        $param = [
            'name' => 'テスト二朗',
            'email' => 'jiro@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('jirojiro'),
        ];
        DB::table('users')->insert($param);
        $param = [
            'name' => 'テスト三郎',
            'email' => 'saburo@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('sabusabu'),
        ];
        DB::table('users')->insert($param);
    }
}
