<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductToOrderRequest;
use App\Http\Requests\OrderRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use App\Models\Order;

class OrderController extends Controller
{

    /**
     * @return Collection
     */
    public function index(): Collection
    {
        return Order::all();
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function show(Order $order): Order
    {
        return $order;
    }

    /**
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = Order::create($request->all());

        return response()->json($order, 201);
    }

    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $order->update($request->all());

        return response()->json($order, 200);
    }

    /**
     * @param Order $order
     * @return JsonResponse
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(null, 204);
    }

    /**
     * @param AddProductToOrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function addProduct(AddProductToOrderRequest $request, Order $order): JsonResponse
    {

        if ($order->payed) {
            return response()->json(['message' => 'Cannot add product to a paid order'], 403);
        }

        $order->products()->attach($request->input('product_id'), ['created_at' => now(), 'updated_at' => now()]);

        return response()->json(['message' => 'Product added to the order successfully']);
    }

}
