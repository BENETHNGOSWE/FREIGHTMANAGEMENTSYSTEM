<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionStoreRequest extends FormRequest
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
            'client_id' => ['required', 'exists:clients,id'],
            'start_date' => ['required', 'date'],
            'expire_date' => ['required', 'date'],
            'package_id' => ['required', 'exists:packages,id'],
            'status' => ['required', 'in:0,1,2'],
        ];
    }
}
