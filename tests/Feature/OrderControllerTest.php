<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker, CreatesTestDataTrait;

    /**
     * @return void
     */
    public function testIndex()
    {

        $customer = $this->createCustomer();

        $this->createOrder(['customer_id' => $customer->id]);

        $response = $this->get('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'customer_id', 'payed', 'created_at', 'updated_at'],
        ]);
        $response->assertJsonCount(1);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id]);

        $response = $this->get("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $order->id,
            'customer_id' => $order->customer_id,
            'payed' => $order->payed,
        ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $customer = $this->createCustomer();

        $data = [
            'customer_id' => $customer->id,
            'payed' => $this->faker->boolean,
        ];

        $response = $this->post('/api/orders', $data);

        $response->assertStatus(201);
        $response->assertJson($data);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {

        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id]);

        $data = [
            'customer_id' => $customer->id,
            'payed' => $this->faker->boolean,
        ];

        $response = $this->put("/api/orders/{$order->id}", $data);

        $response->assertStatus(200);
        $response->assertJson($data);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id]);

        $response = $this->delete("/api/orders/{$order->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /**
     * @return void
     */
    public function testStoreValidation()
    {
        $customer = $this->createCustomer();

        $data = [
            'customer_id' => $customer->id,
            'payed' => $this->faker->boolean,
        ];

        $this->performOrderAction('post', '/api/orders', $data);
    }

    /**
     * @return void
     */
    public function testUpdateValidation()
    {
        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id]);

        $data = [
            'customer_id' => $customer->id,
            'payed' => $this->faker->boolean,
        ];

        $this->performOrderAction('put', "/api/orders/{$order->id}", $data);
    }

    /**
     * Perform common setup and assertions for order actions.
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @return void
     */
    private function performOrderAction(string $method, string $url, array $data): void
    {
        $response = $this->{$method}($url, $data);

        $response->assertStatus($method === 'post' ? 201 : 200);
        $response->assertJson($data);

        $this->assertDatabaseHas('orders', $data);
    }

    /**
     * Test adding a product to the order.
     *
     * @return void
     */
    public function testAddProductToOrder()
    {

        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id, 'payed' => false]);

        $product = Product::factory()->create();

        $response = $this->postJson("/api/orders/{$order->id}/add", [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Product added to the order successfully']);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Test adding a product to a paid order.
     *
     * @return void
     */
    public function testCannotAddProductToPaidOrder()
    {

        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id, 'payed' => true]);

        $product = Product::factory()->create();

        $response = $this->postJson("/api/orders/{$order->id}/add", [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Cannot add product to a paid order']);

        $this->assertDatabaseMissing('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Test validation when adding a product to an order.
     *
     * @return void
     */
    public function testAddProductValidation()
    {
        $customer = $this->createCustomer();

        $order = $this->createOrder(['customer_id' => $customer->id, 'payed' => false]);

        // Missing 'product_id' in the request
        $response = $this->postJson("/api/orders/{$order->id}/add", []);
        $response->assertStatus(422);

        // 'product_id' is present but does not exist in the products table
        $response = $this->postJson("/api/orders/{$order->id}/add", ['product_id' => 999]);
        $response->assertStatus(422);

        // 'product_id' is present and valid
        $product = Product::factory()->create();
        $response = $this->postJson("/api/orders/{$order->id}/add", ['product_id' => $product->id]);
        $response->assertStatus(200);
    }

}
