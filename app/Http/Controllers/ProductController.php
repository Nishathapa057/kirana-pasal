<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function updateStockFromOrder(Order $order)
    {
        try {
            DB::beginTransaction();

            $orderItems = $order->order_items;

            foreach ($orderItems as $orderItem) {
                $productId = $orderItem->product_id;
                $this->updateProductStock($productId);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            // Handle the exception as needed, maybe return an error response
        }
    }

    public function updateProductStock(int $productId)
    {
        try {
            $orderedQuantity = OrderItem::where('product_id', $productId)->sum('quantity');
            $product = Product::findOrFail($productId);

            $newStock = $product->stock - $orderedQuantity;

            if ($newStock < 0) {
                throw new \Exception("Product is out of stock");
            }
            $product->update([
                'stock' => $newStock,
            ]);
        } catch (\Exception $e) {
            report($e);
            // Optionally, rethrow the exception to the calling code
        }
    }
}
