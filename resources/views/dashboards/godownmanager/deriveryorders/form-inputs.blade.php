
@php $editing = isset($deriveryorder) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text name="driver_name" label="Driver Name" :value="old('driver_name', $editing ? $deriveryorder->driver_name : '')" maxlength="255" placeholder="Driver Name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text name="driver_number" label="Driver Number" :value="old('driver_number', $editing ? $deriveryorder->driver_number : '')" maxlength="255"
            placeholder="Driver Number"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text name="vehicle_reg_no" label="Vehicle No" :value="old('vehicle_reg_no', $editing ? $deriveryorder->vehicle_reg_no : '')" maxlength="255"
            placeholder="Vehicle No"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="invoice_id" label="Invoice" required class="select2"
            data-placeholder="Please select the Invoice">
            @php $selected = old('invoice_id', ($editing ? $deriveryorder->invoice_id : '')) @endphp
            <option></option>
            @foreach ($invoices as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
                width: '100%',
                allowClear: true,
                minimumInputLength: 0,
                minimumResultsForSearch: 0,
                placeholder: 'Please select the Invoice',
                matcher: function(params, data) {

                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }
                    return null;
                }
            });
        });
    </script>
@endpush
