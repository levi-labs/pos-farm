<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                'message' => 'Categories fetched successfully',
                'data' => $this->categoryService->getAll(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            // dd($id);
            $category = $this->categoryService->getById($id);
            if ($category) {
                return response()->json([
                    'status' => true,
                    'message' => 'Category fetched successfully',
                    'data' => $category,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'Category not found',
                ], 404);
            }
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->toArray()
            ], 422);
        }

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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->toArray()
            ], 422);
        }

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
