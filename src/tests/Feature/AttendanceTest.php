<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // 4.日時取得機能
    // 5.ステータス確認機能
    // 6.出勤機能
    // 7.休憩機能
    // 8.退勤機能
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
