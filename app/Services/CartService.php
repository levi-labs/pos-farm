<?php


namespace App\Services;

use App\Models\Cart;

class CartService
{

    public function addToCart(array $data)
    {
        try {
            if (empty($data)) {
                throw new \Exception("No data found");
            }

            $cart = Cart::create($data);

            return $cart;
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }
}
