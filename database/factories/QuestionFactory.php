<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment'   => $this->faker->text(random_int(60, 120), true),
            'father_id' => random_int(0,1) && Question::all()->count() > 0  ? Question::all()->random()->id:null,
            'user_id'   => User::all()->random()->id,
        ];
    }

    public function forProduct(Product $product){
        return $this->state(function (array $attributes) use($product){
            return [
                'questionable_id'      => $product->id,
                'questionable_type'    => Product::class,
            ];
        });
    }
}
