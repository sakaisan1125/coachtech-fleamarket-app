<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'user_id'     => \App\Models\User::factory(),
            'name'        => $this->faker->word,
            'description' => $this->faker->sentence,
            'brand'       => $this->faker->optional()->word,
            'price'       => $this->faker->numberBetween(100, 10000),
            'condition'   => $this->faker->randomElement(['新品', '未使用', '美品', '傷や汚れあり']),
            'image_path'  => 'dummy.jpg', // 必要ならダミー画像パス
            'is_sold'     => false,
        ];
    }
}
