@php $editing = isset($customer) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_firstname" label="First Name" :value="old('firstname', $editing ? $customer->customer_firstname : '')" maxlength="255" placeholder="First name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_middlename" label="Middle Name" :value="old('middlename', $editing ? $customer->customer_middlename : '')" maxlength="255"
            placeholder="Middle name"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_lastname" label="Last Name" :value="old('lastname', $editing ? $customer->customer_lastname : '')" maxlength="255" placeholder="Last name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.email name="customer_email" label="Email" :value="old('email', $editing ? $customer->customer_email : '')" maxlength="255" placeholder="Email"
            required></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $customer->phone : '')" maxlength="255" placeholder="Phone"
            required></x-inputs.text>
    </x-inputs.group>
   
</div>
