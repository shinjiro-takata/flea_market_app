<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'seller_id' => User::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 100000),
            'status' => 'on_sale',
            'brand_name' => $this->faker->word(),
            'condition' => '良好',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($item) {
            $categoryIds = Category::inRandomOrder()->limit(rand(2, 3))->pluck('id');
            $item->categories()->attach($categoryIds);
        });
    }
}
