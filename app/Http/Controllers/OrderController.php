<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);
        if ($product->stock < $request->quantity) {
            return response()->json(['message' => 'Not enough stock'], 400);
        }

        $total = $product->price * $request->quantity;
        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_price' => $total,
        ]);

        $product->decrement('stock', $request->quantity);
        return response()->json($order, 201);
    }
}