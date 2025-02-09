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

    public function getById(Category $category)
    {
        return Category::findOrFail($category);
    }

    public function create(Request $request)
    {
        return Category::create($request->all());
    }

    public function update(Request $request, Category $category)
    {
        return $category->update($request->all());
    }

    public function delete(Category $category)
    {
        return $category->delete();
    }
}
