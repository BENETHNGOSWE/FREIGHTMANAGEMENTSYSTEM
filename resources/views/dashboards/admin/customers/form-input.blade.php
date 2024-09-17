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
        <x-inputs.select name="customer_gender" label="customer Gender">
            @php $selected = old('customer_gender', ($editing ? $customer->customer_gender : '')) @endphp
            <option value="male" {{ $selected == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ $selected == 'female' ? 'selected' : '' }}>Female</option>
            <option value="others" {{ $selected == 'others' ? 'selected' : '' }}>Others</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.email name="customer_email" label="Email" :value="old('email', $editing ? $customer->customer_email : '')" maxlength="255" placeholder="Email"
            required></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $customer->phone : '')" maxlength="255" placeholder="Phone"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_address" label="customer Address" :value="old('customer_address', $editing ? $customer->customer_address : '')" maxlength="255"
            placeholder="customer Address"></x-inputs.text>
    </x-inputs.group>

    {{-- <x-inputs.group class="col-sm-4">
        <x-inputs.select name="department_id" label="Department" required>
            @php $selected = old('department_id', ($editing ? $customer->department_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Department</option>
            @foreach ($departments as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group> --}}

    {{-- <x-inputs.group class="col-sm-4">
        <x-inputs.select name="customer_type_id" label="customer Type" required>
            @php $selected = old('customer_type_id', ($editing ? $customer->customer_type_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the customer Type</option>
            @foreach ($customerTypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group> --}}
</div>
