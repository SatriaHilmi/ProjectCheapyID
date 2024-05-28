<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Anda mungkin ingin mengubah ini sesuai dengan logika otorisasi aplikasi Anda
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id', // Pastikan product_id tersedia di tabel products
            'quantity' => 'required|integer|min:1', // Pastikan quantity adalah bilangan bulat positif
            'purchase_price' => 'required|numeric|min:0', // Pastikan purchase_price adalah angka non-negatif
            'purchase_date' => 'required|date', // Pastikan purchase_date adalah tanggal
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product ID is invalid.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least :min.',
            'purchase_price.required' => 'Purchase price is required.',
            'purchase_price.numeric' => 'Purchase price must be a number.',
            'purchase_price.min' => 'Purchase price must be at least :min.',
            'purchase_date.required' => 'Purchase date is required.',
            'purchase_date.date' => 'Purchase date is not a valid date.',
        ];
    }
}