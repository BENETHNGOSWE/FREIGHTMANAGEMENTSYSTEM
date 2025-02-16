@php $editing = isset($salesreport) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text name="receipt" label="Receipt" :value="old('receipt', $editing ? $salesreport->receipt : '')" maxlength="255" placeholder="Receipt" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.date name="date" label="Date" :value="old('date', $editing ? $salesreport->date : '')" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.number name="cbm" label="CBM" :value="old('cbm', $editing ? $salesreport->cbm : '')" step="0.01" required></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text name="nature" label="Nature" :value="old('nature', $editing ? $salesreport->nature : '')" maxlength="255" placeholder="Nature"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text name="hub" label="Hub" :value="old('hub', $editing ? $salesreport->hub : '')" maxlength="255" placeholder="Hub"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.number name="amount1" label="Amount 1" :value="old('amount1', $editing ? $salesreport->amount1 : '')" step="0.01" required></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.number name="amount2" label="Amount 2" :value="old('amount2', $editing ? $salesreport->amount2 : '')" step="0.01"></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.select name="customer_id" label="Customer" required class="select2" data-placeholder="Please select a Customer">
            @php $selected = old('customer_id', $editing ? $salesreport->customer_id : '') @endphp
            <option></option>
            @foreach ($customers as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.select name="staff_id" label="Staff" required class="select2" data-placeholder="Please select a Sales Person">
            @php $selected = old('staff_id', $editing ? $salesreport->staff_id : '') @endphp
            <option></option>
            @foreach ($staffs as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
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
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });
        });
    </script>
@endpush
