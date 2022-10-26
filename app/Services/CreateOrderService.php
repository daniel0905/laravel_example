<?php

namespace App\Services;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 *  This action handle all the task needed to create an order
 */
class CreateOrderService
{
    private $order;
    private $request;
    private $errors;

    /**
     * Create new product
     *
     * @param CreateOrderRequest $request
     * @return Order
     * @throws ValidationException
     */
    public function handle(CreateOrderRequest $request)
    {
        $this->order = new Order();
        $this->request = $request;
        $this->errors = [];  // Store errors and throw a validation error on problems

        DB::beginTransaction();

        $this->fillOrder();
        $this->addUser();
        $this->addBooks();

        if (count($this->errors)) {
            throw ValidationException::withMessages($this->errors);
        }

        DB::commit();

        return $this->order->load(Order::LOAD_WITH);
    }

    private function fillOrder()
    {
        $this->order->fill([
            'note' => $this->request->get('note'),
            'date' => Carbon::now()
        ]);
        $this->order->save();
        $this->order = $this->order->refresh();
    }

    /**
     * Add the user
     */
    public function addUser()
    {
        try {
            $user = User::find(auth()->user()->id);
            $this->order->user()->associate($user)->save();
        } catch (\Error|\Exception $error) {
            $this->errors['addUser'] = "Failed to unknown reason (" . $error->getMessage() . ")";
        }
    }

    /**
     * Add the books
     */
    public function addBooks()
    {
        try {
            if ($this->request->has("books")) {
                foreach ($this->request->get('books') as $book) {
                    $bookDB = Book::find($book['id']);
                    $this->order->books()->attach([
                        $book['id'] => [
                            'quantity' => $book['pivot']['quantity'],
                            'price' => $bookDB->price,
                        ]
                    ]);
                }
            }
        } catch (\Error|\Exception $error) {
            $this->errors['addBooks'] = "Failed to unknown reason (" . $error->getMessage() . ")";
        }
    }
}
