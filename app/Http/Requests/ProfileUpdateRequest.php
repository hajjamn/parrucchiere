<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => [
                'nullable',
                'email',
                'max:255',
                'lowercase',
                Rule::unique('users', 'email')->ignore($this->user()->id)
            ],
            'password' => 'nullable|max:255',
            'role' => 'nullable'
        ];
    }
}
