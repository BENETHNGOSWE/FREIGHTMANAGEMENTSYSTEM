<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', 'string'],
            'logo' => ['nullable', 'max:255', 'string'],
            'address' => ['required', 'max:255', 'string'],
            'country_id' => ['required', 'exists:countries,id'],
            'city' => ['nullable', 'max:255', 'string'],
            'registration_number' => ['required', 'max:255', 'string'],
            'subdomain' => ['nullable', 'max:255', 'string'],
            'client_type_id' => ['required', 'exists:client_types,id'],
        ];
    }
}
