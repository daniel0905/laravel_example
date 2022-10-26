<?php

namespace Tests\Feature\Controller\AuthController;

use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'auth.logout';

    /**
     * Test logout success
     *
     * @return void
     */
    public function testLogoutSuccess()
    {
        // create user
        $response = $this->postJson(route('auth.register'), [
            'name' => Factory::create()->name,
            'email' => Factory::create()->email,
            'password' => 'test123',
            'password_confirmation' => 'test123',
        ]);
        $response->assertCreated();

        // test login success
        $this->payload['email'] = json_decode($response->getContent())->user->email;
        $this->payload['password'] = 'test123';
        $response = $this->postJson(route('auth.login'), $this->payload);
        $response->assertSuccessful();

        // test logout success
        $response = $this->postJson(route($this->routeName));
        $response->assertSuccessful()
            ->assertJson(["message" => "User successfully signed out"]);
    }
}
