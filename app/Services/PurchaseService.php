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
    public function getAll()
    {
        $data = Purchase::with([
            'supplier:id,name',
            'purchaseDetails:purchase_id,quantity,product_id',
            'purchaseDetails.product:id,name'
        ])->get();

        return $data;
    }
    public function getById($id)
    {
        $data = Purchase::find($id);
        if ($data) {
            $purchaseDetails = $data->purchaseDetails()->with('product:id,name')->get();
            return [
                'purchases' => $data,
                'purchases_details' => $purchaseDetails
            ];
        }
        return false;
    }
    public function create(array $data)
    {

        try {
            DB::beginTransaction();
            if (empty($data)) {
                throw new \Exception("No data found");
            }
            $reference_number = format_reference_number('purchase');

            $total_amount = 0;

            $newPurchaseDetails = [];
            foreach ($data['products'] as $product) {
                $product_detail = Product::where('id', $product['product_id'])->first();
                if ($product['quantity'] <= 0) {
                    continue;
                }

                if ($product['quantity'] > $product_detail->quantity_in_stock) {
                    throw new \Exception("Stock not enough");
                }
                $subtotal = $product['quantity'] * $product_detail->price;

                $newPurchaseDetails[] = [
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'discount' => $product['discount'],
                    'price_per_unit' => $product_detail->price,
                    'total_price' => $subtotal
                ];
                $total_amount += $subtotal;

                $product_detail->quantity_in_stock = $product_detail->quantity_in_stock - $product['quantity'];
                $product_detail->update();
            }
            $newPurchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'reference_number' => $reference_number,
                'total_amount' => $total_amount,
                'status' => $data['status'],
            ]);
            $newPurchase->purchaseDetails()->createMany($newPurchaseDetails);
            DB::commit();

            return [
                'purchase' => $newPurchase,
                'purchase_details' => $newPurchaseDetails
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
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
                    $purchaseDetail = $purchase->purchaseDetails()->where('product_id', $product['product_id'])->first();
                    $product_detail = Product::where('id', $product['product_id'])->first();
                    $stock = $product_detail->quantity_in_stock + $purchaseDetail->quantity;

                    if ($product['quantity'] <= 0) {
                        continue;
                    }

                    if ($product['quantity'] > $stock) {
                        throw new \Exception("Stock not enough");
                    }


                    if ($purchaseDetail) {

                        $newPurchaseDetails[] = [
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                        ];

                        $purchaseDetail->update([
                            'quantity' => $product['quantity'],
                        ]);

                        $product_detail->update([
                            'quantity_in_stock' => $stock - $product['quantity'],
                        ]);
                    } else {
                        $newPurchaseDetails[] = [
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                        ];

                        $product_detail->decrement('quantity_in_stock', $product['quantity']);

                        $purchase->purchaseDetails()->create([
                            'product_id' => $product['product_id'],
                            'quantity' => $product['quantity'],
                        ]);
                    }
                }


                return [
                    'purchases' => $purchase,
                    'purchase_details' => $newPurchaseDetails
                ];
            }
            return false;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
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
