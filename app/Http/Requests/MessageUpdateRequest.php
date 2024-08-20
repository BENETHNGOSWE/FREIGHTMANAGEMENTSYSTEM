<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageUpdateRequest extends FormRequest
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
            'sender_id' => ['nullable', 'max:255'],
            'receiver_id' => ['required', 'max:255'],
            'body' => ['required', 'max:255', 'string'],
            'status' => ['required', 'in:0,1,2'],
            'opened' => ['required', 'boolean'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }
}
