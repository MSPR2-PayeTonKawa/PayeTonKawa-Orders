<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\OrderCreatedEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Orders;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Orders::all();

        return response()->json([
            'success' => true,
            'data' => $orders,
            'count' => $orders->count()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        $order = Orders::create([
            'customer_id' => $validatedData['customer_id'],
            'total_amount' => $validatedData['total_amount'],
            'status' => $validatedData['status']
        ]);

        $event = new OrderCreatedEvent();
        $event->publish([
            'id' => $order->id,
            'customer_id' => $order->customer_id,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'items' => $validatedData['items']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $order = Orders::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $order = Orders::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled'
        ]);

        $order->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }

    public function test(): JsonResponse
    {
        return response()->json([
            'service' => 'orders',
            'status' => 'running',
            'message' => 'Orders API is operational',
            'timestamp' => now()->toISOString()
        ]);
    }
}
