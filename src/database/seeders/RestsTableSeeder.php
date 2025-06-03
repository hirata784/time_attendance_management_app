<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 4月
        for ($i = 1; $i <= 30; $i++) {
            $param = [
                'work_id' => (($i * 3) - 2),
                'rest_start' => '2025-04-0' . $i . '  12:00:00',
                'rest_finish' => '2025-04-0' . $i . '  13:00:00',
            ];
            DB::table('rests')->insert($param);
            $param = [
                'work_id' => (($i * 3) - 1),
                'rest_start' => '2025-04-0' . $i . '  12:00:00',
                'rest_finish' => '2025-04-0' . $i . '  13:00:00',
            ];
            DB::table('rests')->insert($param);
            $param = [
                'work_id' => ($i * 3),
                'rest_start' => '2025-04-0' . $i . '  12:00:00',
                'rest_finish' => '2025-04-0' . $i . '  13:00:00',
            ];
            DB::table('rests')->insert($param);
        }

        // 5月
        for ($i = 1; $i <= 31; $i++) {
            $param = [
                'work_id' => (($i * 3) - 2) + 90,
                'rest_start' => '2025-05-0' . $i . '  12:00:00',
                'rest_finish' => '2025-05-0' . $i . '  13:00:00',
            ];
            DB::table('rests')->insert($param);
            $param = [
                'work_id' => (($i * 3) - 1) + 90,
                'rest_start' => '2025-05-0' . $i . '  12:00:00',
                'rest_finish' => '2025-05-0' . $i . '  13:00:00',
            ];
            DB::table('rests')->insert($param);
            $param = [
                'work_id' => ($i * 3) + 90,
                'rest_start' => '2025-05-0' . $i . '  12:00:00',
                'rest_finish' => '2025-05-0' . $i . '  13:00:00',
            ];
            DB::table('rests')->insert($param);
        }
    }
}
