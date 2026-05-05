<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UnavailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isEBuddy();
    }

    public function rules(): array
    {
        return [
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason' => 'nullable|string|max:255',
        ];
    }
}
