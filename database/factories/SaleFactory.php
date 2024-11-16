<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $state = ['negotiation', 'follow_up', 'pending', 'in_progress', 'on_hold', 'closed_lost', 'closed_won', 'cancelled', 'archived'];
        return [
            'description'   => random_int(0, 1) ? $this->faker->paragraphs(random_int(1, 3), true): null,
            'user_id'       => User::all()->random()->id,
            'sale_user_id'  => User::all()->random()->id,
            'state'         => $state[random_int(0, count($state)-1)]
        ];
    }
}
