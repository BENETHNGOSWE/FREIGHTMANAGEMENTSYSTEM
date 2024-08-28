@php $editing = isset($subscriptionToken) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="subscription_id" label="Subscription" required>
            @php $selected = old('subscription_id', ($editing ? $subscriptionToken->subscription_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Subscription</option>
            @foreach($subscriptions as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select
            name="subscription_invoice_id"
            label="Subscription Invoice"
            required
        >
            @php $selected = old('subscription_invoice_id', ($editing ? $subscriptionToken->subscription_invoice_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Subscription Invoice</option>
            @foreach($subscriptionInvoices as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="token_"
            label="Token"
            :value="old('token_', ($editing ? $subscriptionToken->token_ : ''))"
            maxlength="255"
            placeholder="Token"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.date
            name="expire_date"
            label="Expire Date"
            value="{{ old('expire_date', ($editing ? optional($subscriptionToken->expire_date)->format('Y-m-d') : '')) }}"
            max="255"
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="status"
            label="Status"
            :checked="old('status', ($editing ? $subscriptionToken->status : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="client_id" label="Client" required>
            @php $selected = old('client_id', ($editing ? $subscriptionToken->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client</option>
            @foreach($clients as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
