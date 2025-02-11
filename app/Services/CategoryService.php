<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryService
{
    public function getAll()
    {
        return Category::all();
    }

    public function getById($id)
    {
        return Category::find($id);
    }

    public function create(Request $request)
    {
        return Category::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->update($request->all());
            return $category;
        }

        return false;
    }

    public function delete(Category $category)
    {
        return $category->delete();
    }
}
