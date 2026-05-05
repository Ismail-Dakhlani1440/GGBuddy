<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles auth
    }

    public function rules(): array
    {
        return [
            'game_id' => 'required|exists:games,id',
            'rank_id' => 'required|exists:ranks,id',
        ];
    }
}
