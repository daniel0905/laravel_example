<?php

namespace Tests\Feature\Controller\AuthController;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'auth.register';
    private $payload = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->payload['name'] = Factory::create()->name;
        $this->payload['email'] = Factory::create()->email;
        $this->payload['password'] = Factory::create()->password;
        $this->payload['password_confirmation'] = $this->payload['password'];
    }

    public function validationDataProvider()
    {
        return [
            [
                'field' => 'name',
                'value' => null,
                'message' => 'The name field is required.',
            ],
            [
                'field' => 'name',
                'value' => 'unset',
                'message' => 'The name field is required.',
            ],
            [
                'field' => 'name',
                'value' => '',
                'message' => 'The name field is required.',
            ],
            [
                'field' => 'name',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{101}'),
                'message' => 'The name must be between 2 and 100 characters.',
            ],
            [
                'field' => 'name',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{1}'),
                'message' => 'The name must be between 2 and 100 characters.',
            ],
            [
                'field' => 'email',
                'value' => null,
                'message' => 'The email field is required.',
            ],
            [
                'field' => 'email',
                'value' => 'unset',
                'message' => 'The email field is required.',
            ],
            [
                'field' => 'email',
                'value' => '',
                'message' => 'The email field is required.',
            ],
            [
                'field' => 'email',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{20}'),
                'message' => 'The email must be a valid email address.',
            ],
            [
                'field' => 'email',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{101}') . '@gmail.com',
                'message' => 'The email must not be greater than 100 characters.',
            ],
            [
                'field' => 'email',
                'value' => 'unique',
                'message' => 'The email has already been taken.',
            ],
            [
                'field' => 'password',
                'value' => null,
                'message' => 'The password field is required.',
            ],
            [
                'field' => 'password',
                'value' => 'unset',
                'message' => 'The password field is required.',
            ],
            [
                'field' => 'password',
                'value' => '',
                'message' => 'The password field is required.',
            ],
            [
                'field' => 'password',
                'value' => 'confirmed',
                'message' => 'The password confirmation does not match.',
            ],
            [
                'field' => 'password',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{5}'),
                'message' => 'The password must be at least 6 characters.',
            ],
        ];
    }

    /**
     * Test validation
     *
     * @dataProvider validationDataProvider
     *
     * @param $field
     * @param $value
     * @param $message
     * @return void
     */
    public function testValidation($field, $value, $message)
    {
        $this->payload[$field] = $value;
        if ($field === 'password') {
            $this->payload['password_confirmation'] = $value;
        }
        if ($value === 'unset') {
            unset($this->payload[$field]);
        }

        if ($value === 'unique') {
            $user = User::factory()->create()->toArray();
            $this->payload[$field] = $user[$field];
        }

        if ($value === 'confirmed') {
            $this->payload['password'] = Factory::create()->password;
            $this->payload['password_confirmation'] = Factory::create()->password;
        }

        $response = $this->postJson(route($this->routeName), $this->payload);
        $response->assertInvalid([$field => $message]);
    }

    /**
     * Test success register
     *
     * @return void
     */
    public function testSuccessRegister()
    {
        $response = $this->postJson(route($this->routeName), $this->payload);
        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'name' => $this->payload['name'],
            'email' => $this->payload['email']
        ]);
    }
}
