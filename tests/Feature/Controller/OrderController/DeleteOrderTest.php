<?php

namespace Tests\Feature\Controller\OrderController;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteOrderTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'orders.deleteOrder';

    /**
     * Test delete not found
     *
     * @return void
     */
    public function testDeleteNotFound()
    {
        $id = '12312313123132';
        $response = $this->deleteJson(route($this->routeName, $id));
        $response->assertNotFound();
    }

    /**
     * Test delete success
     *
     * @return void
     */
    public function testDeleteSuccess()
    {
        // create order
        $payload = [];
        $payload['note'] = Factory::create()->paragraph;
        $payload['books'][0] = Book::factory()->create()->toArray();
        $payload['books'][0]['pivot']['quantity'] = Factory::create()->numerify;
        $payload['books'][1] = Book::factory()->create()->toArray();
        $payload['books'][1]['pivot']['quantity'] = Factory::create()->numerify;
        $payload['books'][2] = Book::factory()->create()->toArray();
        $payload['books'][2]['pivot']['quantity'] = Factory::create()->numerify;
        $responseOrder = $this->postJson(route('orders.createOrder'), $payload);
        $responseOrder->assertCreated();

        $response = $this->deleteJson(route($this->routeName, $responseOrder->json('id')));
        $response->assertSuccessful();

        $this->assertDatabaseMissing('orders', ['id' => $responseOrder->json('id')]);
        $this->assertDatabaseMissing('order_detail', ['order_id' => $responseOrder->json('id')]);
    }
}
