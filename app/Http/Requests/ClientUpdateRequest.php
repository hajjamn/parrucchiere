<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
                Rule::unique('clients', 'email')->ignore($this->client()->id())
            ],
            'phone' => 'nullable|max:20',
            'birth_date' => 'nullable|date|before_or_equal:today',

        ];
    }
}
