<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SalesService;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Sales fetched successfully',
            'data' => $this->salesService->getAll(),
        ], 200);
    }

    public function show($id)
    {
        try {
            $sales = $this->salesService->getById($id);
            if ($sales) {
                return response()->json([
                    'status' => true,
                    'message' => 'Sales fetched successfully',
                    'data' => $sales,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Sales not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'total_discount' => 'required',
            'payment_method' => 'required',
            'payment_status' => 'required',
            'total_amount' => 'required',
            'status' => 'required',
            'note' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required',
            'products.*.discount' => 'nullable|numeric',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validator->errors()->toArray(),
            ], 422);
        }
        try {
            $data = $this->salesService->create($validator->validated());
            return response()->json([
                'status' => true,
                'message' => 'Sales created successfully',
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
