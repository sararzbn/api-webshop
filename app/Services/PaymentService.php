<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentService implements PaymentProviderInterface
{

    /**
     * @param array $attributes
     * @return array
     */
    public function processPayment(array $attributes): array
    {
        $response = Http::post('https://superpay.view.agentur-loop.com/pay', $attributes);

        return $response->json();
    }
}
