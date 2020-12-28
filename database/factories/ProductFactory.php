<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->sentence(2);
        $slug = Str::slug($name, '-');

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->unique()->sentence(30),
            'price' => rand(50000, 1000000),
            'stock' => rand(1, 1000),
        ];
    }
}
