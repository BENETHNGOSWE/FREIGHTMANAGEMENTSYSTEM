@php $editing = isset($customerlead) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_firstname" label="First Name" :value="old('firstname', $editing ? $customerlead->customer_firstname : '')" maxlength="255"
            placeholder="First name" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_middlename" label="Middle Name" :value="old('middlename', $editing ? $customerlead->customer_middlename : '')" maxlength="255"
            placeholder="Middle name"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="customer_lastname" label="Last Name" :value="old('lastname', $editing ? $customerlead->customer_lastname : '')" maxlength="255"
            placeholder="Last name" ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="category_id" label="Category" required>
            @php $selected = old('category_id', ($editing ? $customerlead->category_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Category</option>
            @foreach ($customercategories as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.email name="customer_email" label="Email" :value="old('email', $editing ? $customerlead->customer_email : '')" maxlength="255" placeholder="Email"
            ></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $customerlead->phone : '')" maxlength="15" placeholder="Phone" required
            type="tel" pattern="^\+255[0-9]{9}$" inputmode="numeric" title="Phone number must start with +255 and be followed by 9 digits.">
        </x-inputs.text>
    </x-inputs.group>



</div>
