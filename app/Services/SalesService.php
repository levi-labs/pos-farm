<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SalesService
{
    protected $inventoryMovements;
    protected $authUser;

    public function __construct(InventoryMovementService $inventoryMovements)
    {
        $this->inventoryMovements = $inventoryMovements;
        $this->authUser = Auth('sanctum')->user()->id;
    }
    public function getAll(): Collection
    {
        return Sales::all();
    }
    public function getById($id): ?array
    {
        $sales = Sales::find($id);
        if ($sales) {
            $salesDetail = SalesDetail::with('product:id,name')->where('sales_id', $id)->get();
            return [
                'sales' => $sales,
                'sales_details' => $salesDetail
            ];
        }
        return null;
    }

    public function create($sales): array
    {
        DB::beginTransaction();
        try {
            $createdSales = Sales::create([
                'customer_id' => $sales['customer_id'],
                'total_amount' => $sales['total_amount'],
                'total_discount' => $sales['total_discount'],
                'payment_status' => $sales['payment_status'],
                'status' => $sales['status'],
                'payment_method' => $sales['payment_method'],
                'note' => $sales['note'],
            ]);
            $createdSalesDetails = [];

            foreach ($sales['products'] as $product) {

                $product_detail = Product::where('id', $product['product_id'])->first();
                if ($product['quantity'] <= 0) {
                    continue;
                }

                if ($product['quantity'] > $product_detail->quantity_in_stock) {
                    throw new \Exception("Stock not enough");
                }

                $createdSalesDetails[] = SalesDetail::create([
                    'sales_id' => $createdSales->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'discount' => $product['discount'],
                    'price_per_unit' => $product_detail->price,
                    'total_price' => $product_detail->price * $product['quantity']
                ]);

                $product_detail->decrement('quantity_in_stock', $product['quantity']);
                $this->inventoryMovements->create([
                    'product_id' => $product['product_id'],
                    'movement_type' => 'out',
                    'quantity' => $product['quantity'],
                    'price_per_unit' => $product_detail->price,
                    'total_value' => $product_detail->price * $product['quantity'],
                    'reference' => "Sales-" . $createdSales->id,
                    'created_by' => $this->authUser,
                    'note' => $sales['note']
                ]);
            }

            DB::commit();
            return [
                'sales' => $createdSales,
                'sales_details' => $createdSalesDetails,

            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update($id, $sales): array
    {
        DB::beginTransaction();
        try {
            $createdSales = Sales::where('id', $id)->first();
            $createdSales->update([
                'customer_id' => $sales['customer_id'] ?? $createdSales['customer_id'],
                'total_amount' => $sales['total_amount'] ?? $createdSales['total_amount'],
                'total_discount' => $sales['total_discount'] ?? $createdSales['total_discount'],
                'payment_status' => $sales['payment_status'] ?? $createdSales['payment_status'],
                'status' => $sales['status'] ?? $createdSales['status'],
                'payment_method' => $sales['payment_method'] ?? $createdSales['payment_method'],
                'note' => $sales['note'] ?? $createdSales['note'],
            ]);
            $createdSalesDetails = [];

            foreach ($sales['products'] as $product) {
                $product_detail = Product::where('id', $product['product_id'])->first();
                if ($product['quantity'] <= 0) {
                    continue;
                }

                if ($product['quantity'] > $product_detail->quantity_in_stock) {
                    throw new \Exception("Stock not enough");
                }

                if (SalesDetail::where('sales_id', $id)->where('id', $product['detail_id'])->first()) {
                    $createdSalesDetails[] = SalesDetail::where('sales_id', $id)->where('id', $product['detail_id'])->first()->update([
                        'product_id' => $product['product_id'] ?? $createdSalesDetails['product_id'],
                        'quantity' => $product['quantity'] ?? $createdSalesDetails['quantity'],
                        'discount' => $product['discount'] ?? $createdSalesDetails['discount'],
                        'price_per_unit' => $product_detail->price,
                        'total_price' => $product_detail->price * $product['quantity']
                    ]);
                } else {
                    $createdSalesDetails[] = SalesDetail::create([
                        'sales_id' => $id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'discount' => $product['discount'],
                        'price_per_unit' => $product_detail->price,
                        'total_price' => $product_detail->price * $product['quantity'],
                    ]);
                }

                $this->inventoryMovements->create([
                    'product_id' => $product['product_id'],
                    'movement_type' => 'out',
                    'quantity' => $product['quantity'],
                    'price_per_unit' => $product_detail->price,
                    'total_value' => $product_detail->price * $product['quantity'],
                    'reference' => "Sales-" . $createdSales->id,
                    'created_by' => $this->authUser,
                    'note' => $sales['note']
                ]);
            }
            DB::commit();
            return [
                'sales' => $createdSales,
                'sales_details' => $createdSalesDetails
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function delete($id): bool
    {
        return Sales::where('id', $id)->delete();
    }
}
