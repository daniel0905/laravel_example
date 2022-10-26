<?php

namespace Tests\Feature\Controller\AuthController;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'auth.changePassword';
    private $user = null;
    private $payload = [];

    public function setUp(): void
    {
        parent::setUp();

        // create user
        $response = $this->postJson(route('auth.register'), [
            'name' => Factory::create()->name,
            'email' => Factory::create()->email,
            'password' => 'test123',
            'password_confirmation' => 'test123',
        ]);
        $response->assertCreated();

        $this->user = User::find($response->json('user')['id']);
        $this->actingAs($this->user);

        $this->payload['old_password'] = 'test123';
        $this->payload['new_password'] = Factory::create()->password;
        $this->payload['new_password_confirmation'] = $this->payload['new_password'];
    }

    public function validationDataProvider()
    {
        return [
            [
                'field' => 'old_password',
                'value' => null,
                'message' => 'The old password field is required.',
            ],
            [
                'field' => 'old_password',
                'value' => 'unset',
                'message' => 'The old password field is required.',
            ],
            [
                'field' => 'old_password',
                'value' => '',
                'message' => 'The old password field is required.',
            ],
            [
                'field' => 'old_password',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{5}'),
                'message' => 'The old password must be at least 6 characters.',
            ],
            [
                'field' => 'new_password',
                'value' => null,
                'message' => 'The new password field is required.',
            ],
            [
                'field' => 'new_password',
                'value' => 'unset',
                'message' => 'The new password field is required.',
            ],
            [
                'field' => 'new_password',
                'value' => '',
                'message' => 'The new password field is required.',
            ],
            [
                'field' => 'new_password',
                'value' => 'confirmed',
                'message' => 'The new password confirmation does not match.',
            ],
            [
                'field' => 'new_password',
                'value' => Factory::create()->regexify('[A-Za-z0-9]{5}'),
                'message' => 'The new password must be at least 6 characters.',
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
        if ($field === 'new_password') {
            $this->payload['new_password_confirmation'] = $value;
        }
        if ($value === 'unset') {
            unset($this->payload[$field]);
        }

        if ($value === 'confirmed') {
            $this->payload['new_password'] = Factory::create()->password;
            $this->payload['new_password_confirmation'] = Factory::create()->password;
        }

        $response = $this->patchJson(route($this->routeName), $this->payload);
        $response->dump();
        $response->assertInvalid([$field => $message]);
    }

    /**
     * Test change password success
     *
     * @return void
     */
    public function testChangePasswordSuccess() {
        $response = $this->patchJson(route($this->routeName), $this->payload);
        $response->assertSuccessful();

        // login
        $response = $this->postJson(route('auth.login'), [
            'email' => $this->user->email,
            'password' => $this->payload['new_password'],
        ]);
        $response->assertSuccessful();
    }
}
