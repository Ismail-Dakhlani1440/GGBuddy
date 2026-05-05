<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles auth
    }

    public function rules(): array
    {
        return [
            'display_name' => 'required|string|max:255',
            'timezone' => 'required|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:1000',
            'banner' => 'nullable|image|max:4096|dimensions:min_width=1200,min_height=400',
            'browser_notifications' => 'nullable|boolean',
            'sound_enabled' => 'nullable|boolean',
        ];
    }
}
