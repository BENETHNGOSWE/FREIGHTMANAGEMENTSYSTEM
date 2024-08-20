<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionInvoiceUpdateRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'balance' => ['required', 'numeric'],
            'bill_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:0,1,2'],
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }
}
