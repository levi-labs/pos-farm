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

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'total_discount' => 'required',
            'payment_method' => 'required',
            'payment_status' => 'required',
            'total_amount' => 'required',
            'status' => 'required',
            'note' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validated->errors()->toArray(),
            ], 422);
        }

        // $this->purchaseService->store($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Purchase created successfully',
            'data' => null,
        ], 201);
    }
}
