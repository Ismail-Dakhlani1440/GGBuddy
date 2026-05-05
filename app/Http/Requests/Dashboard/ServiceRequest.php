<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isEBuddy();
    }

    public function rules(): array
    {
        return [
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
        ];
    }
}
