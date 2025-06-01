<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

class Correction_workFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'work_id' => Work::factory(),
            'application_status' => $this->faker->randomNumber(),
            'attendance_time' => $this->faker->date(),
            'leaving_time' => $this->faker->date(),
            'remarks' => $this->faker->word(),
            'application_date' => $this->faker->date(),
        ];
    }
}
