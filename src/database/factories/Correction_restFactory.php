<?php

namespace Database\Factories;

use App\Models\Correction_work;
use Illuminate\Database\Eloquent\Factories\Factory;

class Correction_restFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'correction_work_id' => Correction_work::factory(),
            'rest_start' => $this->faker->date(),
            'rest_finish' => $this->faker->date(),
        ];
    }
}
