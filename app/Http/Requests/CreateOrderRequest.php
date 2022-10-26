<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'note' => 'nullable|string',
            'books' => 'required|array',
            'books.*.id' => 'required|numeric|exists:books,id',
            'books.*.pivot.quantity' => 'required|numeric|min:1',
        ];
    }
}
