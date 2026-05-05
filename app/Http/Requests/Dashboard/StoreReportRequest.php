<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    public function rules(): array
    {
        return [
            'target_id' => 'required|exists:users,id',
            'reason'    => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.min' => 'Please provide a more detailed reason (at least 10 characters).',
        ];
    }
}
