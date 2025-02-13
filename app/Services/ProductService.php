<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function getAll()
    {
        return Product::all();
    }
    public function getById($id)
    {
        $product = Product::find($id);
        if ($product) {
            return $product;
        }
        return false;
    }

    public function create(Request $request)
    {
        return Product::create($request->all());
    }
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($request->all());
            return $product;
        }
        return false;
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            return $product->delete();
        }
        return false;
    }
}
