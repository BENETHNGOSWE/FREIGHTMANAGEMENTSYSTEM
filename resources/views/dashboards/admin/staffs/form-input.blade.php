@php $editing = isset($staff) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="staff_firstname" label="First Name" :value="old('firstname', $editing ? $staff->staff_firstname : '')" maxlength="255" placeholder="First name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="staff_middlename" label="Middle Name" :value="old('middlename', $editing ? $staff->staff_middlename : '')" maxlength="255"
            placeholder="Middle name"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="staff_lastname" label="Last Name" :value="old('lastname', $editing ? $staff->staff_lastname : '')" maxlength="255" placeholder="Last name"
            required></x-inputs.text>
    </x-inputs.group>


    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="staff_gender" label="Staff Gender">
            @php $selected = old('staff_gender', ($editing ? $staff->staff_gender : '')) @endphp
            <option value="male" {{ $selected == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ $selected == 'female' ? 'selected' : '' }}>Female</option>
            <option value="others" {{ $selected == 'others' ? 'selected' : '' }}>Others</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.email name="staff_email" label="Email" :value="old('email', $editing ? $staff->staff_email : '')" maxlength="255" placeholder="Email"
            required></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $staff->phone : '')" maxlength="255" placeholder="Phone"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="staff_address" label="Staff Address" :value="old('staff_address', $editing ? $staff->staff_address : '')" maxlength="255"
            placeholder="Staff Address"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="branch_id" label="Branch" required>
            @php $selected = old('branch_id', ($editing ? $staff->branch_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Branch</option>
            @foreach ($branches as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="staff_type_id" label="Staff Type" required>
            @php $selected = old('staff_type_id', ($editing ? $staff->staff_type_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Staff Type</option>
            @foreach ($staffTypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
