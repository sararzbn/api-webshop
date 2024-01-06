<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\PaymentProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{

    use RefreshDatabase, CreatesTestDataTrait;


    /**
     * Test successful payment.
     *
     * @return void
     * @throws Exception
     */
    public function testPayOrderSuccess()
    {
        $paymentServiceMock = $this->mock(PaymentProviderInterface::class);
        $paymentServiceMock->shouldReceive('processPayment')->andReturn(['message' => 'Payment Successful']);


        $customer = $this->createCustomer();
        $order = $this->createOrder(['customer_id' => $customer->id, 'payed' => false]);

        $response = $this->postJson("/api/orders/{$order->id}/pay", [
            'customer_email' => 'user@email.com',
            'value' => 50.0,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Payment Successful']);

        $this->assertEquals(1, Order::find($order->id)->payed);

    }

    /**
     * Test failed payment.
     *
     * @return void
     * @throws Exception
     */
    public function testPayOrderFailed()
    {
        // Mock the PaymentProviderInterface
        $paymentServiceMock = $this->mock(PaymentProviderInterface::class);
        $paymentServiceMock->shouldReceive('processPayment')->andReturn(['message' => 'Insufficient Funds']);

        $customer = $this->createCustomer();
        $order = $this->createOrder(['customer_id' => $customer->id, 'payed' => false]);

        $response = $this->postJson("/api/orders/{$order->id}/pay", [
            'customer_email' => 'user@email.com',
            'value' => 50.0,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Payment Failed: Insufficient Funds']);

        $this->assertEquals(0, Order::find($order->id)->payed);
    }

    /**
     * Test validation rules for paying an order.
     *
     * @return void
     */
    public function testPayOrderValidation()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder(['customer_id' => $customer->id, 'payed' => false]);

        // Case 1: Missing customer_email
        $response = $this->postJson('/api/orders/' . $order->id . '/pay', [
            'value' => 50.0,
        ]);

        $response->assertStatus(422); // Expecting a validation error

        // Case 2: Invalid customer_email format
        $response = $this->postJson('/api/orders/' . $order->id . '/pay', [
            'customer_email' => 'invalid-email',
            'value' => 50.0,
        ]);

        $response->assertStatus(422);

        // Case 3: Missing value
        $response = $this->postJson('/api/orders/' . $order->id . '/pay', [
            'customer_email' => 'user@email.com',
        ]);

        $response->assertStatus(422);

        // Case 4: Invalid value format
        $response = $this->postJson('/api/orders/' . $order->id . '/pay', [
            'customer_email' => 'user@email.com',
            'value' => 'invalid-numeric-value',
        ]);

        $response->assertStatus(422);

    }

}
