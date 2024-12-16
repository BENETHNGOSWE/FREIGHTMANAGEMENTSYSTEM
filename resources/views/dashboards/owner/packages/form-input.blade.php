@php $editing = isset($kodapackage) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="name" label="Package Name" :value="old('name', $editing ? $kodapackage->name : '')" maxlength="255" placeholder="Package name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="monthly_fee" label="Monthly Fee" :value="old('monthly fee', $editing ? $kodapackage->monthly_fee : '')" maxlength="255"
            placeholder="Monthly Fee"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="yearly_fee" label="Yearly Fee" :value="old('yearly fee', $editing ? $kodapackage->yearly_fee : '')" maxlength="255" placeholder="Yearly Fee"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="client_admin_id" label="Client Admin" required>
            @php $selected = old('client_admin_id', ($editing ? $kodapackage->client_admin_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Branch</option>
            @foreach ($clientadmins as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="staff_type_id" label="Add Package" required>
            @php $selected = old('staff_type_id', ($editing ? $kodapackage->staff_type_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Package Type</option>
            @foreach ($staffTypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
