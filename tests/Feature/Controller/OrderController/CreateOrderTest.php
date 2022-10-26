<?php

namespace Tests\Feature\Controller\OrderController;

use App\Models\Book;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use DatabaseTransactions;

    private $routeName = 'orders.createOrder';
    private $payload = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->payload['note'] = Factory::create()->paragraph;
        $this->payload['books'][0] = Book::factory()->create()->toArray();
        $this->payload['books'][0]['pivot']['quantity'] = Factory::create()->numerify;
        $this->payload['books'][1] = Book::factory()->create()->toArray();
        $this->payload['books'][1]['pivot']['quantity'] = Factory::create()->numerify;
        $this->payload['books'][2] = Book::factory()->create()->toArray();
        $this->payload['books'][2]['pivot']['quantity'] = Factory::create()->numerify;
    }

    public function validationDataProvider()
    {
        return [
            [
                'field' => 'books',
                'value' => null,
                'messageField' => 'books',
                'message' => 'The books field is required.',
            ],
            [
                'field' => 'books',
                'value' => 'unset',
                'messageField' => 'books',
                'message' => 'The books field is required.',
            ],
            [
                'field' => 'books',
                'value' => '',
                'messageField' => 'books',
                'message' => 'The books field is required.',
            ],
            [
                'field' => 'books',
                'value' => Factory::create()->lexify,
                'messageField' => 'books',
                'message' => 'The books must be an array.',
            ],
            [
                'field' => 'books.*.id',
                'value' => null,
                'messageField' => 'books.0.id',
                'message' => 'The books.0.id field is required.',
            ],
            [
                'field' => 'books.*.id',
                'value' => 'unset',
                'messageField' => 'books.0.id',
                'message' => 'The books.0.id field is required.',
            ],
            [
                'field' => 'books.*.id',
                'value' => '',
                'messageField' => 'books.0.id',
                'message' => 'The books.0.id field is required.',
            ],
            [
                'field' => 'books.*.id',
                'value' => Factory::create()->lexify,
                'messageField' => 'books.0.id',
                'message' => 'The books.0.id must be a number.',
            ],
            [
                'field' => 'books.*.id',
                'value' => '1231212124',
                'messageField' => 'books.0.id',
                'message' => 'The selected books.0.id is invalid.',
            ],
            [
                'field' => 'books.*.pivot.quantity',
                'value' => null,
                'messageField' => 'books.0.pivot.quantity',
                'message' => 'The books.0.pivot.quantity field is required.',
            ],
            [
                'field' => 'books.*.pivot.quantity',
                'value' => 'unset',
                'messageField' => 'books.0.pivot.quantity',
                'message' => 'The books.0.pivot.quantity field is required.',
            ],
            [
                'field' => 'books.*.pivot.quantity',
                'value' => '',
                'messageField' => 'books.0.pivot.quantity',
                'message' => 'The books.0.pivot.quantity field is required.',
            ],
            [
                'field' => 'books.*.pivot.quantity',
                'value' => Factory::create()->lexify,
                'messageField' => 'books.0.pivot.quantity',
                'message' => 'The books.0.pivot.quantity must be a number.',
            ],
            [
                'field' => 'books.*.pivot.quantity',
                'value' => 0,
                'messageField' => 'books.0.pivot.quantity',
                'message' => 'The books.0.pivot.quantity must be at least 1.',
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
    public function testValidation($field, $value, $messageField, $message)
    {
        if ($field === 'books.*.id') {
            $this->payload['books'][0]['id'] = $value;
        } elseif ($field === 'books.*.pivot.quantity') {
            $this->payload['books'][0]['pivot']['quantity'] = $value;
        } else {
            $this->payload[$field] = $value;
        }

        if ($value === 'unset') {
            if ($field === 'books.*.id') {
                unset($this->payload['books'][0]['id']);
            } elseif ($field === 'books.*.pivot.quantity') {
                unset($this->payload['books'][0]['pivot']['quantity']);
            } else {
                unset($this->payload[$field]);
            }
        }

        $response = $this->postJson(route($this->routeName), $this->payload);
        $response->assertInvalid([$messageField => $message]);
    }

    /**
     * Test create success
     *
     * @return void
     */
    public function testCreateSuccess()
    {
        $response = $this->postJson(route($this->routeName), $this->payload);
        $response->assertCreated();

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->loginUser->id,
            'note' => $this->payload['note'],
            'date' => Carbon::now()->toDateString(),
        ]);

        foreach ($this->payload['books'] as $book) {
            $this->assertDatabaseHas('order_detail', [
                'order_id' => $response->json('id'),
                'book_id' => $book['id'],
                'quantity' => $book['pivot']['quantity'],
                'price' => Book::find($book['id'])->price,
            ]);
        }
    }
}
