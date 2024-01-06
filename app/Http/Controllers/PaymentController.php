<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayOrderRequest;
use App\Models\Order;
use App\Services\PaymentProviderInterface;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{

    /**
     * @param PayOrderRequest $request
     * @param Order $order
     * @param PaymentProviderInterface $paymentService
     * @return JsonResponse
     */
    public function payOrder(PayOrderRequest $request, Order $order, PaymentProviderInterface $paymentService): JsonResponse
    {
        $paymentResponse = $paymentService->processPayment([
            'order_id' => $order->id,
            'customer_email' => $request->input('customer_email'),
            'value' => $request->input('value'),
        ]);

        if ($paymentResponse['message'] === 'Payment Successful') {
            $order->update(['payed' => true]);

            return response()->json(['message' => 'Payment Successful']);

        } else {
            return response()->json(['message' => 'Payment Failed: ' . $paymentResponse['message']], 400);
        }
    }

}
