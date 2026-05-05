<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Permission checked in Policy via Controller
        return true;
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
