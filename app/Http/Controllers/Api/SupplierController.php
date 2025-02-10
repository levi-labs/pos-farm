<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

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
            'data' => $this->supplierService->getAll(),
            'message' => 'Suppliers fetched successfully',
        ], 200);
    }

    public function show(Supplier $supplier)
    {
        try {
            $supplier = $this->supplierService->getById($supplier);
            if ($supplier) {
                return response()->json([
                    'status' => true,
                    'data' => $supplier,
                    'message' => 'Supplier fetched successfully',
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
        try {
            return response()->json([
                'status' => true,
                'data' => $this->supplierService->create($request),
                'message' => 'Supplier created successfully',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        try {
            $supplier = $this->supplierService->update($request, $supplier);
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
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier_data = $this->supplierService->delete($supplier);
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
