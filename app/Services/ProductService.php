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

    public function search($query)
    {
        $query = Product::query();

        if ($query['name']) {
            $query->where('name', 'like', '%' . $query['name'] . '%');
        }
        if ($query['category']) {
            $query->whereHas('category', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query['category'] . '%');
            });
        }

        return $query->paginate(10);
    }
    public function getById($id)
    {
        $product = Product::find($id);
        if ($product) {
            return $product;
        }
        return false;
    }

    public function create($data)
    {
        return Product::create($data);
    }
    public function update($data, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($data);
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
