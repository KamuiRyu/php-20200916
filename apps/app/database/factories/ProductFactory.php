<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'code' => $this->faker->randomNumber(5),
            'status' => $this->faker->randomElement(['draft', 'trash', 'published']),
            'url' => $this->faker->url(),
            'creator' => $this->faker->name(),
            'product_name' => $this->faker->word(),
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'brands' => $this->faker->word(),
            'categories' => $this->faker->words(3, true), 
            'labels' => $this->faker->words(2, true),
            'cities' => $this->faker->city(),
            'purchase_places' => $this->faker->word(),
            'stores' => $this->faker->company(),
            'ingredients_text' => $this->faker->sentence(),
            'traces' => $this->faker->words(2, true),
            'serving_size' => $this->faker->randomNumber(2),
            'serving_quantity' => $this->faker->randomFloat(2, 0.1, 10),
            'nutriscore_score' => $this->faker->numberBetween(-10, 10),
            'nutriscore_grade' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'main_category' => $this->faker->word(),
            'image_url' => $this->faker->imageUrl(),
            'imported_t' => now(),
            'created_t' => now(),
            'last_modified_t' => now(),
        ];        
    }
}
