<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplier = [

            'PT. Sumber Alam Sejahtera',
            'PT. Karya Mandiri Abadi',
            'PT. Mitra Karya Nusantara',
            'PT. Cahaya Abadi Sentosa',
            'PT. Indonesia Gemilang',
            'PT. Sumber Rejeki Makmur',
            'PT. Mega Jaya Perkasa',
            'PT. Nusantara Cemerlang',
            'PT. Sukses Selalu Abadi',
            'PT. Harapan Bersama Sentosa'

        ];
        return [
            'name' => fake()->randomElement($supplier),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'description' => fake()->sentence(5),
        ];
    }
}
