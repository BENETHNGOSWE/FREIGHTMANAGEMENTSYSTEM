@php $editing = isset($load) @endphp

<div class="row load-package-row">
    
    {{-- <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_no[{{ $index }}]" label="Load No" :value="old('load_no.' . $index, $editing ? $load->load_no : '')" maxlength="255" placeholder="Load No" required></x-inputs.text>
    </x-inputs.group> --}}

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_name[{{ $index }}]" label="Load Name" :value="old('load_name.' . $index, $editing ? $load->load_name : '')" maxlength="255" placeholder="Load Name"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_trucking_number[{{ $index }}]" label="Load Trucking Number" :value="old('load_trucking_number.' . $index, $editing ? $load->load_trucking_number : '')" maxlength="255" placeholder="Trucking Number" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_description[{{ $index }}]" label="Description" :value="old('load_description.' . $index, $editing ? $load->load_description : '')" maxlength="255" placeholder="Description" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_price[{{ $index }}]" label="Load Price" :value="old('load_price.' . $index, $editing ? $load->load_price : '')" maxlength="255" placeholder="Load Price" required></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_transportfee[{{ $index }}]" label="Transport Fee" :value="old('load_transportfee.' . $index, $editing ? $load->load_transportfee : '')" maxlength="255" placeholder="Transport Fee" required></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.date name="load_transport_date[{{ $index }}]" label="Transport Date" :value="old('load_transport_date.' . $index, $editing ? $load->load_transport_date : '')" maxlength="255" placeholder="Transport Date" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.date name="load_delivery_date[{{ $index }}]" label="Delivery Date" :value="old('load_delivery_date.' . $index, $editing ? $load->load_delivery_date : '')" maxlength="255" placeholder="Delivery Date" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="loadpackagetype_id[{{ $index }}]" label="Load Type" required>
            @php $selected = old('loadpackagetype_id.' . $index, ($editing ? $load->loadpackagetype_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the load Type</option>
            @foreach ($loadpackagetypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="load_status[{{ $index }}]" label="Status" required>
            @php
                $statuses = ['instock', 'onmovement', 'delivery', 'closed'];
            @endphp
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ old('load_status.' . $index, ($editing && $load->load_status == $status) ? 'selected' : '') }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
    

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_weight_value[{{ $index }}]" label="Operator Comment" :value="old('load_weight_value.' . $index, $editing ? $load->load_weight_value : '')" maxlength="255" placeholder="Operator Comment" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="load_weight_unit[{{ $index }}]" label="Unit" id="load_weight_unit_{{ $index }}" onchange="toggleWeightInputs({{ $index }})" required>
            <option value="" selected disabled>Select Unit</option>
            <option value="0" {{ old('load_weight_unit.' . $index, ($editing && $load->load_weight_unit == '0') ? 'selected' : '') }}>CBM</option>
            <option value="1" {{ old('load_weight_unit.' . $index, ($editing && $load->load_weight_unit == '1') ? 'selected' : '') }}>KG</option>
        </x-inputs.select>
    </x-inputs.group>

    <div class="col-sm-4 mt-4">
        <button type="button" class="btn btn-danger remove-loadpackage-btn">
            Remove Load
        </button>
    </div>
</div>
