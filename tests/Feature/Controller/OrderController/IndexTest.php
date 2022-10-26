<?php

namespace Tests\Feature\Controller\OrderController;

use App\Models\Order;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'orders.index';

    /**
     * Test search with pagination
     *
     * @return void
     */
    public function testSearchPagination()
    {
        Order::factory(10)->for(User::factory())->create();
        $search = [
            'page' => 1,
            'limit' => 10,
        ];

        $response = $this->getJson(route($this->routeName, $search));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');

        $search = [
            'page' => $response['total'] + 1,
            'limit' => 10,
        ];
        $this->getJson(route($this->routeName, $search))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Test search userId
     *
     * @return void
     * @throws \Throwable
     */
    public function testSearchUserId()
    {
        $user = User::factory()->create();
        $count = Factory::create()->numberBetween(1, 50);
        Order::factory($count)->state(['user_id' => $user->id])->create();

        $response = $this->getJson(route($this->routeName, ['user_id' => $user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $data = $response->decodeResponseJson()['data'];
        $this->assertGreaterThanOrEqual($count, count($data));

    }

    /**
     * Test filter min_date
     *
     * @return void
     * @throws \Throwable
     */
    public function testFilterMinDate()
    {
        Order::factory(10)
            ->state(['date' => Factory::create()->dateTimeBetween('-1 days', '-1 days')->format('Y-m-d')])
            ->for(User::factory())
            ->create();
        Order::factory(5)
            ->state(['date' => Factory::create()->dateTimeBetween('-5 days', '-5 days')->format('Y-m-d')])
            ->for(User::factory())
            ->create();

        $response = $this->getJson(route($this->routeName, ['min_date' => Factory::create()->dateTimeBetween('-4 days', '-4 days')->format('Y-m-d')]));
        $response->assertStatus(Response::HTTP_OK);
        $data = $response->decodeResponseJson()['data'];
        $this->assertGreaterThanOrEqual(10, count($data));
    }

    /**
     * Test filter max_date
     *
     * @return void
     * @throws \Throwable
     */
    public function testFilterMaxDate()
    {
        Order::factory(10)
            ->state(['date' => Factory::create()->dateTimeBetween('+5 days', '+5 days')->format('Y-m-d')])
            ->for(User::factory())
            ->create();
        Order::factory(5)
            ->state(['date' => Factory::create()->dateTimeBetween('+10 days', '+10 days')->format('Y-m-d')])
            ->for(User::factory())
            ->create();

        $response = $this->getJson(route($this->routeName, ['max_date_created' => Factory::create()->dateTimeBetween('+7 days', '+7 days')->format('Y-m-d')]));
        $response->assertStatus(Response::HTTP_OK);
        $data = $response->decodeResponseJson()['data'];
        $this->assertGreaterThanOrEqual(10, count($data));
    }
}
