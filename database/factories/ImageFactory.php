<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typeImage = [
            'animals',
            'cats',
            'dogs',
            'babys',
            'cars',
            'bikers',
            'games'
        ];

        $img640x480 = $this->faker->imageUrl(640, 480, $typeImage[random_int(0, (count($typeImage)-1))], true);
        $img1000x1000 = $this->faker->imageUrl(1000, 1000, $typeImage[random_int(0, (count($typeImage)-1))], true);

        return [
            'resized'       => $img640x480,
            'authentic'     => $img1000x1000,
            'path'          => $img640x480,
            'resized_path'  => $img1000x1000
        ];
    }

    public function forProduct(Product $product){
        return $this->state(function (array $attributes) use($product){
            return [
                'order'             => 1,
                'imageable_id'      => $product->id,
                'imageable_type'    => Product::class,
            ];
        });
    }
}
