<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    protected $purchaseService;
    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index()
    {
        try {

            $data = $this->purchaseService->getAll();
            return response()->json([
                'status' => true,
                'message' => 'Purchases fetched successfully',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'total_discount' => 'required',
            'payment_method' => 'required',
            'payment_status' => 'required',
            'status' => 'required',
            'note' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.discount' => 'nullable|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validated->errors()->toArray(),
            ], 422);
        }

        $data = $this->purchaseService->create($validated->validated());

        return response()->json([
            'status' => true,
            'message' => 'Purchase created successfully',
            'data' => $data,
        ], 201);
    }
}
