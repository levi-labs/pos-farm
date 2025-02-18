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
    public function getAll(): Collection
    {
        return Sales::all();
    }
    public function getById($id): ?Sales
    {
        $sales = Sales::find($id);
        if ($sales) {
            $salesDetail = SalesDetail::where('sales_id', $id)->get();
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
            }
            // dd($createdSalesDetails);

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

    public function update($id, $sales, $salesDetail): array
    {
        DB::beginTransaction();
        try {
            $createdSales = Sales::where('id', $id)->update($sales);
            $createdSalesDetails = [];
            foreach ($salesDetail as $detail) {
                $createdSalesDetails[] = SalesDetail::where('sales_id', $id)->update($detail);
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
