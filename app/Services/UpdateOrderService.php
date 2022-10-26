<?php

namespace App\Services;

use App\Http\Requests\UpdateOrderRequest;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 *  This action handle all the task needed to update an order
 */
class UpdateOrderService
{
    private $order;
    private $request;
    private $errors;

    /**
     * Update an order
     *
     * @param UpdateOrderRequest $request
     * @param Order $order
     * @return Order
     * @throws ValidationException
     */
    public function handle(UpdateOrderRequest $request, Order $order)
    {
        $this->order = $order;
        $this->request = $request;
        $this->errors = [];  // Store errors and throw a validation error on problems

        DB::beginTransaction();

        $this->fillOrder();
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
        ]);
        $this->order->save();
        $this->order = $this->order->refresh();
    }

    /**
     * Add the books
     */
    public function addBooks()
    {
        try {
            if ($this->request->has("books")) {
                $this->order->books()->detach();
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
