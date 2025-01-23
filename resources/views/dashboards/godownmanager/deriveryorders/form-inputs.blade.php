@php $editing = isset($deriveryorder) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="driver_name"
            label="load Type Name"
            :value="old('driver_name', ($editing ? $deriveryorder->driver_name : ''))"
            maxlength="255"
            placeholder="Load type Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="driver_number"
            label="Load Type Description"
            :value="old('driver_number', ($editing ? $deriveryorder->driver_number : ''))"
            maxlength="255"
            placeholder="Load Type Description"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="vehicle_reg_no"
            label="Load Type Description"
            :value="old('vehicle_reg_no', ($editing ? $deriveryorder->vehicle_reg_no : ''))"
            maxlength="255"
            placeholder="Load Type Description"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="invoice_id" label="Invoice" required class="searchable">
            @php $selected = old('invoice_id', ($editing ? $deriveryorder->invoice_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Invoice</option>
            @foreach ($invoices as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
    
</div>
