<?php


namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    protected $cartService;
    protected $authUser;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->authUser = Auth('sanctum')->user()->id;
    }
    public function create(array $data)
    {

        try {
            DB::beginTransaction();
            if (empty($data)) {
                throw new \Exception("No data found");
            }

            $newPurchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'reference_number' => $data['reference_number'],
                'total_amount' => $data['total_amount'],
                'status' => $data['status'],
            ]);

            $newPurchaseDetails = [];
            foreach ($data['products'] as $product) {
                $product_detail = Product::where('id', $product['product_id'])->first();
                if ($product['quantity'] <= 0) {
                    continue;
                }

                if ($product['quantity'] > $product_detail->quantity_in_stock) {
                    throw new \Exception("Stock not enough");
                }

                $newPurchaseDetails[] = [
                    'purchase_id' => $newPurchase->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'discount' => $product['discount'],
                ];
                $cart = $this->cartService->addToCart([
                    'quantity' => $product['quantity'],
                    'product_id' => $product['product_id'],
                ]);
            }
            $newPurchase->purchaseDetails()->createMany($newPurchaseDetails);
            DB::commit();

            return [
                'purchases' => $newPurchase,
                'purchases_details' => $newPurchaseDetails,
                'cart' => $cart
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th->getMessage();
        }
    }

    public function update(array $data, $id)
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::find($id);
            $newPurchaseDetails = [];
            if ($purchase) {
                foreach ($data['products'] as $product) {
                    $product_detail = Product::where('id', $product['product_id'])->first();
                    if ($product['quantity'] <= 0) {
                        continue;
                    }
                    if ($product['quantity'] > $product_detail['quantity_in_stock']) {
                        throw new \Exception("Stock not enough");
                    }

                    $purchaseDetail = $purchase->purchaseDetails()->where('product_id', $product['product_id'])->first();
                    if ($purchaseDetail) {
                        $newPurchaseDetails[] = [
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                            'discount' => $product['discount'],
                        ];

                        $stock = $product_detail->quantity_in_stock + $purchaseDetail->quantity;

                        $purchaseDetail->update([
                            'quantity' => $product['quantity'],
                            'discount' => $product['discount'],
                        ]);
                        $product_detail->update([
                            'quantity_in_stock' => $stock - $product['quantity'],
                        ]);
                    } else {
                        $newPurchaseDetails[] = [
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                            'discount' => $product['discount'],
                        ];

                        $product_detail->decrement('quantity_in_stock', $product['quantity']);

                        $purchase->purchaseDetails()->create([
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                            'discount' => $product['discount'],
                        ]);
                    }
                }
                DB::commit();

                return [
                    'purchases' => $purchase,
                    'purchase_details' => $newPurchaseDetails
                ];
            }
            return false;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $purchase = Purchase::findOrFail($id);
            $purchase->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th->getMessage();
        }
    }
}
