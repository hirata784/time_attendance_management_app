<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // 16.メール認証機能
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
