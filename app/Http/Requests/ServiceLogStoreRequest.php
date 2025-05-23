<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceLogStoreRequest extends FormRequest
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
    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'performed_at' => 'required|date',
            'custom_prices' => 'nullable|array',
            'custom_prices.*' => 'nullable|numeric|min:0',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|integer|min:0',
        ];
    }
}
