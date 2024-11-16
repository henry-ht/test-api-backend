<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paragraphs = $this->faker->paragraphs(rand(1, 4));
        $title = $this->faker->realText(50);
        $post = "<h1>{$title}</h1>";
        foreach ($paragraphs as $para) {
            $post .= "<p>{$para}</p>";
        }
        
        return [
            'name'          => $this->faker->colorName(),
            'price'         => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 999999),
            'description'   => random_int(0, 1) ? $post : null,
            'quantity'      => $this->faker->randomDigit(),
            'deleted_by'    => null,
            'longitude'     => $this->faker->longitude(),
            'latitude'      => $this->faker->latitude(),
            'user_id'       => User::all()->random()->id,
        ];
    }
}
