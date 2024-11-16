<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message'   => $this->faker->paragraphs(random_int(1, 2), true),
        ];
    }

    public function forSale(Sale $sale){
        return $this->state(function (array $attributes) use($sale){

            if(random_int(0,1)){
                $userId     = $sale->user_id;
                $toUserId   = $sale->sale_user_id;
            }else{
                $userId     = $sale->sale_user_id;
                $toUserId   = $sale->user_id;
            }
            return [
                'messageable_id'    => $sale->id,
                'messageable_type'  => Sale::class,
                'user_id'           => $userId,
                'to_user_id'        => $toUserId,
            ];
        });
    }
}
