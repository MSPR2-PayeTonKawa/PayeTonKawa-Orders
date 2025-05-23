<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/orders/api/test", function () {
    return response()->json([
        "status" => "success",
        "service" => "orders-api",
        "message" => "Le service Orders API fonctionne correctement",
        "data" => [
            "orders_count" => 18,
            "pending_orders" => 3,
            "version" => "1.0.0"
        ]
    ]);
});