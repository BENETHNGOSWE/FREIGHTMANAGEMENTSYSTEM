<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionTokenUpdateRequest extends FormRequest
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
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'subscription_invoice_id' => [
                'required',
                'exists:subscription_invoices,id',
            ],
            'token_' => ['required', 'max:255', 'string'],
            'expire_date' => ['nullable', 'date'],
            'status' => ['required', 'boolean'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }
}
