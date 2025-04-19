<?php

namespace Database\Factories\v1;

use App\Models\v1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Log>
 */
class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => $this->faker->word(),
            'description' => $this->faker->sentence(), 
            'user_id' => User::factory(),  
        ];;
    }
}
