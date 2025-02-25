<?php


namespace App\Services;

use App\Models\Cart;

class CartService
{

    public function getAll()
    {
        try {
            return Cart::with('product')->get();
        } catch (\Exception $th) {
            throw $th->getMessage();
        }
    }
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

    public function delete($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();
            return true;
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }
}
