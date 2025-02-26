<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Products fetched successfully',
                'data' => $this->productService->getAll(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Products fetched failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validator->errors()->toArray(),
            ], 422);
        }
        try {
            return response()->json([
                'status' => true,
                'message' => 'Products fetched successfully',
                'data' => $this->productService->search($validator->validated()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Products fetched failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $product = $this->productService->getById($id);
            if ($product) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product fetched successfully',
                    'data' => $product,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product fetched failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {

        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'sku' => 'required|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validated->errors()->toArray(),
            ], 422);
        }

        try {
            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => $this->productService->create($validated->validated()),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Product created failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        // for validation SKU must be unique and handle if same sku is passed

        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'sku' => 'required|unique:products,sku,' . $id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validated->errors()->toArray(),
            ], 422);
        }

        try {
            $product_data = $this->productService->update($validated->validated(), $id);
            if ($product_data) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product updated successfully',
                    'data' => $product_data
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product updated failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product_data = $this->productService->delete($id);
            if ($product_data) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product deleted failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
