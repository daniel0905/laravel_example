<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $loginUser = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginUser = User::factory()->create();
        $this->actingAs(User::find($this->loginUser->id));
    }
}
