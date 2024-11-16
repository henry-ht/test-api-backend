<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class FakerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:faker-data {model?} {--items=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = (string)$this->argument('model');
        $totalItems = (int)$this->option('items');

        if($totalItems <= 0){
            $totalItems = 10;
        }

        switch ($model) {
            case 'product':
                $this->createProduct($totalItems);
                break;

            case 'user':
                $this->createUser($totalItems);
                break;

            case 'sale':
                $this->createSale($totalItems);
                break;

            default:
                $this->createUser($totalItems);
                $this->createProduct($totalItems);
                $this->createSale($totalItems);
                break;
        }

    }

    private function createUser(int $totalItems){
        \App\Models\User::factory($totalItems)->create();
    }

    private function createSale(int $totalItems){
        $products = \App\Models\Product::all();
        \App\Models\Sale::factory($totalItems)->create()
            ->each(function ($sale) use($products){
                $product = $products->random();
                $sale->products()->attach($product->id, [
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'description'   => json_encode($product->description),
                    'sold_quantity' => $product->quantity,
                ]);
                \App\Models\Message::factory(random_int(1,5))->forSale($sale)->create();
            });
    }

    private function createProduct(int $totalItems){
        $categoriesAge = Category::where('type', 'age')->get();
        $categoriesColor = Category::where('type', 'color')->get();
        $categoriesGender = Category::where('type', 'gender')->get();

        \App\Models\Product::factory($totalItems)->create()
            ->each(function ($product) use($categoriesAge, $categoriesColor, $categoriesGender) {
                $product->categories()->attach($categoriesAge->random()->id);
                $product->categories()->attach($categoriesColor->random()->id);
                $product->categories()->attach($categoriesGender->random()->id);

                \App\Models\Image::factory(random_int(1,10))->forProduct($product)->create();
                \App\Models\Question::factory(random_int(1,5))->forProduct($product)->create();
            });
    }
}
