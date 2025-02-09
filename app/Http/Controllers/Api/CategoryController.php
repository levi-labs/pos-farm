<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->categoryService->getAll(),
                'message' => 'Categories fetched successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function show(Category $category)
    {
        try {
            $category = $this->categoryService->getById($category);
            return response()->json([
                'status' => true,
                'data' => $category,
                'message' => 'Category fetched successfully',
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;            
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->categoryService->create($request),
                'message' => 'Category created successfully',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = $this->categoryService->update($request, $id);
            if ($category) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category updated successfully',
                    'data' => $category
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category_data = $this->categoryService->delete($category);
            if ($category_data) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category deleted successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
