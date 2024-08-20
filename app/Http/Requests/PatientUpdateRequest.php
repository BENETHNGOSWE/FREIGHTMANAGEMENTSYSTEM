<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientUpdateRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'patientID' => ['required', 'max:255', 'string'],
            'has_insurance' => ['required', 'boolean'],
            'city' => ['nullable', 'max:255', 'string'],
        ];
    }
}
