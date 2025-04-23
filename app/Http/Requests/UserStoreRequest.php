<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UserStoreRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
            'role' => 'nullable|string',
            'master_password' => 'required|string'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('master_password') !== env('MASTER_PASSWORD')) {
                $validator->errors()->add('master_password', 'La master password è errata.');
            }
        });
    }
}
