<?php

namespace App\Services;

use App\Models\InventoryMovement;
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
        $updatedSales = Sales::where('id', $id)->first();

        DB::beginTransaction();
        try {
            if ($updatedSales) {
                $updatedSales->update([
                    'customer_id' => $sales['customer_id'] ?? $updatedSales['customer_id'],
                    'total_amount' => $sales['total_amount'] ?? $updatedSales['total_amount'],
                    'total_discount' => $sales['total_discount'] ?? $updatedSales['total_discount'],
                    'payment_status' => $sales['payment_status'] ?? $updatedSales['payment_status'],
                    'status' => $sales['status'] ?? $updatedSales['status'],
                    'payment_method' => $sales['payment_method'] ?? $updatedSales['payment_method'],
                    'note' => $sales['note'] ?? $updatedSales['note'],
                ]);

                $updatedInventoryMovements = [];
                $updatedSalesDetails = [];
                foreach ($sales['products'] as $key => $product) {
                    $salesDetail = SalesDetail::where('sales_id', $id)
                        ->where('product_id', $product['product_id'])
                        ->first();

                    $product_detail = Product::where('id', $product['product_id'])->first();

                    $inventoryMovement = InventoryMovement::where('product_id', $product['product_id'])
                        ->where('reference', "Sales-" . $id)
                        ->first();

                    if ($product['quantity'] <= 0) {
                        continue;
                    }

                    if ($product['quantity'] > $product_detail->quantity_in_stock) {
                        throw new \Exception("Stock not enough");
                    }

                    if ($salesDetail) {
                        $stock = $product_detail->quantity_in_stock + $salesDetail->quantity;

                        $salesDetail->update([
                            'product_id' => $product['product_id'] ?? $salesDetail['product_id'],
                            'quantity' => $product['quantity'] ?? $salesDetail['quantity'],
                            'discount' => $product['discount'] ?? $salesDetail['discount'],
                            'price_per_unit' => $product_detail->price,
                            'total_price' => $product_detail->price * $product['quantity']
                        ]);

                        $product_detail->update([
                            'quantity_in_stock' => $stock - $product['quantity'],
                        ]);

                        $inventoryMovement->update([
                            'quantity' => $product['quantity'],
                            'price_per_unit' => $product_detail->price,
                            'total_value' => $product_detail->price * $product['quantity'],
                        ]);
                        $updatedInventory = $inventoryMovement->fresh();
                        $updatedSalesDetails[] = $salesDetail;
                        $updatedInventoryMovements[] = $updatedInventory;
                    } else {

                        $newSalesDetail = SalesDetail::create([
                            'sales_id' => $id,
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                            'discount' => $product['discount'],
                            'price_per_unit' => $product_detail->price,
                            'total_price' => $product_detail->price * $product['quantity'],
                        ]);


                        $product_detail->decrement('quantity_in_stock', $product['quantity']);

                        $createNewMovement = $this->inventoryMovements->create([
                            'product_id' => $product['product_id'],
                            'movement_type' => 'out',
                            'quantity' => $product['quantity'],
                            'price_per_unit' => $product_detail->price,
                            'total_value' => $product_detail->price * $product['quantity'],
                            'reference' => "Sales-" . $updatedSales->id,
                            'created_by' => $this->authUser,
                            'note' => $sales['note']
                        ]);

                        $updatedSalesDetails[] = $newSalesDetail;
                        $updatedInventoryMovements[] = $createNewMovement;
                    }
                }
                DB::commit();
                return [
                    'sales' => $updatedSales,
                    'sales_details' => $updatedSalesDetails,
                    'inventory_movements' => $updatedInventoryMovements

                ];
            } else {
                throw new \Exception("Sales not found");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            $sales = Sales::where('id', $id)->first();
            if ($sales) {

                $salesDetails = SalesDetail::where('sales_id', $id)->get();
                $updateHistoryInventoryMovement = [];
                foreach ($salesDetails as $salesDetail) {
                    $product = Product::where('id', $salesDetail->product_id)->first();
                    $inventory_movement = InventoryMovement::where('product_id', $salesDetail->product_id)
                        ->where('reference', "Sales-" . $id)
                        ->first();
                    //create history inventory movement cancel stock
                    $updateHistoryInventoryMovement[] = InventoryMovement::create([
                        'product_id' => $salesDetail->product_id,
                        'movement_type' => $inventory_movement->movement_type == 'out' ? 'in' : 'out',
                        'quantity' => $salesDetail->quantity,
                        'price_per_unit' => $salesDetail->price_per_unit,
                        'total_value' => $salesDetail->total_price,
                        'reference' => "Cancel Sales-" . $id,
                        'created_by' => $this->authUser,
                        'note' => "Cancellation of sales transaction #" . $id
                    ]);

                    $product->increment('quantity_in_stock', $salesDetail->quantity);
                }
                $sales->update([
                    'status' => 'cancelled',
                    'payment_status' => 'unpaid'
                ]);

                DB::commit();

                return [
                    'sales' => $sales,
                    'inventory_movement' => $updateHistoryInventoryMovement
                ];
            } else {
                throw new \Exception("Sales not found");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
