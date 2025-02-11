<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Elektronik',
            'Pakaian',
            'Makanan & Minuman',
            'Perabot Rumah Tangga',
            'Kecantikan',
            'Olahraga',
            'Automotif',
            'Buku',
            'Mainan & Permainan',
            'Perhiasan',
            'Teknologi',
            'Kesehatan',
            'Hobi & Kerajinan',
            'Alat Tulis',
            'Musik',
        ];
        return [
            'name' => fake()->randomElement($categories),
            'description' => fake()->sentence(5),
        ];
    }
}
