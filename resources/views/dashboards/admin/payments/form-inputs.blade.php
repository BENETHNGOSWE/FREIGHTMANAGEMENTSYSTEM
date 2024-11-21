@php $editing = isset($payment) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="Bank_name"
            label="Bank Name"
            :value="old('Bank_name', ($editing ? $payment->Bank_name : ''))"
            maxlength="255"
            placeholder="Bank Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="account_name"
            label="Account Name"
            :value="old('account_name', ($editing ? $payment->account_name : ''))"
            maxlength="255"
            placeholder="Account Name"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="account_number"
            label="Account No"
            :value="old('account_number', ($editing ? $payment->account_number : ''))"
            maxlength="255"
            placeholder="Account No"
        ></x-inputs.text>
    </x-inputs.group>

   
</div>
