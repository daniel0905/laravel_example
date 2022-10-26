<?php

namespace Tests\Feature\Controller\AuthController;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'auth.login';
    private $payload = [];

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test login fail
     *
     * @return void
     */
    public function testLoginFail() {
        $this->payload['email'] = Factory::create()->email;
        $this->payload['password'] = Factory::create()->password;
        $response = $this->postJson(route($this->routeName), $this->payload);
        $response->assertUnauthorized();
    }

    /**
     * Test login success
     *
     * @return void
     */
    public function testLoginSuccess() {
        // create user
        $response = $this->postJson(route('auth.register'), [
            'name' => Factory::create()->name,
            'email' => Factory::create()->email,
            'password' => 'test123',
            'password_confirmation' => 'test123',
        ]);
        $response->assertCreated();

        $this->payload['email'] = json_decode($response->getContent())->user->email;
        $this->payload['password'] = 'test123';
        $response = $this->postJson(route($this->routeName), $this->payload);
        $response->assertSuccessful();
    }
}
