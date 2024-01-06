<?php

namespace App\Services;

interface PaymentProviderInterface
{

    /**
     * @param array $attributes
     * @return array
     */
    public function processPayment(array $attributes): array;

}
