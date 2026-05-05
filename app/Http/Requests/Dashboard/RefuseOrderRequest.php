<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class RefuseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controller handles authorization via Gate
    }

    public function rules(): array
    {
        return [
            'refuse_reason' => 'required|string|min:5|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'refuse_reason.required' => 'Please provide a reason for refusing the order.',
            'refuse_reason.min' => 'The reason must be at least 5 characters long.',
        ];
    }
}
