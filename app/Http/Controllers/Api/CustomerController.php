<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Customers fetched successfully',
            'data' => $this->customerService->getAll(),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validator->errors()->toArray(),
            ], 422);
        }
        try {
            $data =  $this->customerService->create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully',
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        };
    }

    public function show($id)
    {
        try {
            $customer = $this->customerService->getById($id);
            if ($customer) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer fetched successfully',
                    'data' => $customer,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = $this->customerService->update($request->all(), $id);
            if ($customer) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer updated successfully',
                    'data' => $customer,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $customer = $this->customerService->delete($id);
            if ($customer) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer deleted successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
