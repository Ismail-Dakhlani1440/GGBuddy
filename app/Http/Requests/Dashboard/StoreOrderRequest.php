<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles auth
    }

    public function rules(): array
    {
        return [
            'hours'   => 'required|numeric|min:1|max:24',
            'message' => 'nullable|string|max:500',
        ];
    }
}
