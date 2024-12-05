<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Cuisine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cuisine>
 */
class CuisineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cuisineNames = [
            'Spaghetti Carbonara',
            'Chicken Tikka Masala',
            'Sushi Roll',
            'Beef Stroganoff',
            'Pad Thai',
            'Margherita Pizza',
            'Caesar Salad',
            'Vegetable Spring Rolls',
            'Clam Chowder',
            'Butter Chicken',
        ];

        return [
            'name' => $this->faker->randomElement($cuisineNames), // Random cuisine name
            'description' => $this->faker->text(250), // Text up to 200 characters
            'image' => 'images/image-not-found.jpg', // Placeholder image
            'price' => $this->faker->randomFloat(2, 5.99, 30.99), // Float between 5.99 and 30.99
            'category_id' => Category::inRandomOrder()->first()->id, // Random category ID
        ];
    }
}
