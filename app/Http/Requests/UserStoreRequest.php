<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', 'string'],
            'email' => ['required', 'unique:users,email', 'email'],
            'password' => ['required'],
            'phone' => ['nullable', 'max:255', 'string'],
            'address' => ['required', 'max:255', 'string'],
            'registration_date' => ['required', 'date'],
            'photo' => ['nullable', 'file'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'status' => ['required', 'in:0,1,2,3,4'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'roles' => 'array',
        ];
    }
}
