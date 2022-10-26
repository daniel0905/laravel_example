<?php

namespace Tests\Feature\Controller\OrderController;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GetOrderTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'orders.getOrder';

    /**
     * Test get not found
     *
     * @return void
     */
    public function testGetNotFound()
    {
        $id = '12312313123132';
        $response = $this->getJson(route($this->routeName, $id));
        $response->assertNotFound();
    }

    /**
     * Test get article success
     *
     * @return void
     */
    public function testGetSuccess()
    {
        $data = Order::factory()->for(User::factory())->create();
        $response = $this->getJson(route($this->routeName, $data->id));
        $response->assertSuccessful();
    }
}
