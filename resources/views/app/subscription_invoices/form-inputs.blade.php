@php $editing = isset($subscriptionInvoice) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.number
            name="amount"
            label="Amount"
            :value="old('amount', ($editing ? $subscriptionInvoice->amount : ''))"
            max="255"
            step="0.01"
            placeholder="Amount"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.number
            name="balance"
            label="Balance"
            :value="old('balance', ($editing ? $subscriptionInvoice->balance : ''))"
            max="255"
            step="0.01"
            placeholder="Balance"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.date
            name="bill_date"
            label="Bill Date"
            value="{{ old('bill_date', ($editing ? optional($subscriptionInvoice->bill_date)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.date
            name="due_date"
            label="Due Date"
            value="{{ old('due_date', ($editing ? optional($subscriptionInvoice->due_date)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="status" label="Status">
            @php $selected = old('status', ($editing ? $subscriptionInvoice->status : '')) @endphp
            <option value="0" {{ $selected == '0' ? 'selected' : '' }} >0</option>
            <option value="1" {{ $selected == '1' ? 'selected' : '' }} >1</option>
            <option value="2" {{ $selected == '2' ? 'selected' : '' }} >2</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="subscription_id" label="Subscription" required>
            @php $selected = old('subscription_id', ($editing ? $subscriptionInvoice->subscription_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Subscription</option>
            @foreach($subscriptions as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="client_id" label="Client" required>
            @php $selected = old('client_id', ($editing ? $subscriptionInvoice->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client</option>
            @foreach($clients as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
