<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Suppliers fetched successfully',
            'data' => $this->supplierService->getAll(),
        ], 200);
    }

    public function show($id)
    {
        try {
            $supplier = $this->supplierService->getById($id);
            if ($supplier) {
                return response()->json([
                    'status' => true,
                    'message' => 'Supplier fetched successfully',
                    'data' => $supplier,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'error' => 'Supplier not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'description' => 'required',
            'email' => 'required',
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
                'message' => 'Supplier created successfully',
                'data' => $this->supplierService->create($request),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Supplier created failed',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $supplier = $this->supplierService->update($request, $id);
            if ($supplier) {
                return response()->json([
                    'status' => true,
                    'message' => 'Supplier updated successfully',
                    'data' => $supplier
                ], 200);
            }
            return response()->json([
                'status' => false,
                'error' => 'Supplier not found',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Supplier created failed',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $supplier_data = $this->supplierService->delete($id);
            if ($supplier_data) {
                return response()->json([
                    'status' => true,
                    'message' => 'Supplier deleted successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Supplier not found',
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
