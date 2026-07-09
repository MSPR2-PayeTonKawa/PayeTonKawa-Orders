<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with('items')->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|uuid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $total = 0;

            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'total_amount' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => $total]);

            return $order->load('items');
        });

        return response()->json($order, 201);
    }

    public function show(string $id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Commande introuvable'], 404);
        }

        return response()->json($order, 200);
    }

    public function update(Request $request, string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Commande introuvable'], 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,paid,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed',
        ]);

        $order->update($validated);

        return response()->json($order->load('items'), 200);
    }

    public function destroy(string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Commande introuvable'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Commande supprimée avec succès'], 200);
    }
}