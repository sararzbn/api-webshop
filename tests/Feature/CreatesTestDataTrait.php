<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;

trait CreatesTestDataTrait
{
    /**
     * @return Customer
     */
    protected function createCustomer(): Customer
    {
        return Customer::factory()->create();
    }

    /**
     * @param array $attributes
     * @return Order
     */
    protected function createOrder(array $attributes = []): Order
    {
        return Order::factory()->create($attributes);
    }
}
