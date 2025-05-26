<?php

namespace Database\Factories;

use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'work_id' => Work::factory(),
            'rest_start' => $this->faker->date(),
            'rest_finish' => $this->faker->date(),
        ];
    }
}
