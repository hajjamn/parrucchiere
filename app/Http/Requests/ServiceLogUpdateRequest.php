<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceLogUpdateRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'performed_at' => 'required|date|before_or_equal:' . now()->addMinute(),
            'custom_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'is_part_of_subscription' => 'nullable|boolean'
        ];
    }
}
