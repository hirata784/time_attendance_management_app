<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 4月
        for ($j = 1; $j <= 30; $j++) {
            for ($k = 1; $k <= 3; $k++) {
                $param = [
                    'user_id' => $k,
                    'attendance_time' => '2025-04-0' . $j . ' 09:00:00',
                    'leaving_time' => '2025-04-0' . $j . ' 18:00:00',
                ];
                DB::table('works')->insert($param);
            }
        }

        // 5月
        for ($j = 1; $j <= 31; $j++) {
            for ($k = 1; $k <= 3; $k++) {
                $param = [
                    'user_id' => $k,
                    'attendance_time' => '2025-05-0' . $j . ' 09:00:00',
                    'leaving_time' => '2025-05-0' . $j . ' 18:00:00',
                ];
                DB::table('works')->insert($param);
            }
        }
    }
}
