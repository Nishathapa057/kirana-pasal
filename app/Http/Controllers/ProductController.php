<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // public function updateStockFromOrder(Order $order)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $orderItems = $order->order_items;

    //         foreach ($orderItems as $orderItem) {
    //             $productId = $orderItem->product_id;
    //             $this->updateProductStock($productId);
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         report($e);
    //         // Handle the exception as needed, maybe return an error response
    //     }
    // }

    // public function updateProductStock(int $productId)
    // {
    //     try {
    //         $orderedQuantity = OrderItem::where('product_id', $productId)->sum('quantity');
    //         $product = Product::findOrFail($productId);
    //         $product->stock = $product->qty;
    //         $newStock = $product->stock - $orderedQuantity;

    //         if ($newStock < 0) {
    //             throw new \Exception("Product is out of stock");
    //         }
    //         $product->update([
    //             'stock' => $newStock,
    //         ]);
    //     } catch (\Exception $e) {
    //         report($e);
    //         // Optionally, rethrow the exception to the calling code
    //     }
    // }

    public function updateStockFromOrder(Order $order)
    {
        try {
            DB::beginTransaction();

            $orderItems = $order->order_items;

            foreach ($orderItems as $orderItem) {
                $productId = $orderItem->product_id;
                $this->updateProductStock($productId, $orderItem->quantity);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            // Handle the exception as needed, maybe return an error response
        }
    }

    public function updateProductStock(int $productId, int $orderedQuantity)
    {
        try {
            $product = Product::findOrFail($productId);
            if ($product->quantity < $orderedQuantity) {
                throw new \Exception("Insufficient stock for product.");
            }
            $product->update([
                'stock' => $product->quantity,
            ]);

            $newStock = $product->fresh()->stock - $orderedQuantity;

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

    public function store(Request $request)
    {
        // Your existing order creation logic here

        $productData = [
            'name' => $request->input('name'),
            'barcode' => $request->input('barcode'),
            'image' => $request->input('image'),
            'description' => $request->input('description'),
            'qty' => $request->input('qty'),
            'stock' => $request->input('stock'),
            'price' => $request->input('price'),
        ];

        $product = Order::create($productData);

        // Call the method to update the stock
        $this->updateStockFromOrder($product);

        // Return the response
    }
}
