@php $editing = isset($load) @endphp

<div class="row">
    <input type="hidden" name="customer_id" value="{{ request()->customer_id }}">

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_no" label="Load No" :value="old('Load No', $editing ? $load->load_no : '')" maxlength="255" placeholder="Load No"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_name" label="Load Name" :value="old('Load Name', $editing ? $load->load_name : '')" maxlength="255"
            placeholder="Load Name"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_location" label="Load Location" :value="old('Load Location', $editing ? $load->load_location : '')" maxlength="255" placeholder="Location"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_description" label="Description" :value="old('Description', $editing ? $load->load_description : '')" maxlength="255" placeholder="Description"
            required></x-inputs.text>
    </x-inputs.group>


    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="loadpackagetype_id" label="load Type" required>
            @php $selected = old('loadpackagetype_id', ($editing ? $load->loadpackagetype_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the load Type</option>
            @foreach ($loadpackagetypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
