<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:games,title,' . $this->route('game')->id,
            'description' => 'nullable|string|min:10',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'ranks' => 'nullable|array',
            'ranks.*.id' => 'nullable',
            'ranks.*.title' => 'required_with:ranks|string|max:100',
            'ranks.*.tier' => 'required_with:ranks|integer|min:1',
            'ranks.*.icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];
    }
}
