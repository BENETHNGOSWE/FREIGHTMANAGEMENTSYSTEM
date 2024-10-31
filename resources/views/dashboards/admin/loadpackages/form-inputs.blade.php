@php $editing = isset($load) @endphp

<div class="row load-package-row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_name[{{ $index }}]" label="Load Name" :value="old('load_name.' . $index, $editing ? $load->load_name : '')" maxlength="255"
            placeholder="Load Name"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_trucking_number[{{ $index }}]" label="Load Trucking Number" :value="old('load_trucking_number.' . $index, $editing ? $load->load_trucking_number : '')"
            maxlength="255" placeholder="Trucking Number" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.textarea name="load_description[{{ $index }}]" label="Description" :value="old('load_description.' . $index, $editing ? $load->load_description : '')"
            maxlength="255" placeholder="Description" required></x-inputs.textarea>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_price[{{ $index }}]" label="Load Price" :value="old('load_price.' . $index, $editing ? $load->load_price : '')" maxlength="255"
            placeholder="Load Price" required></x-inputs.number>
    </x-inputs.group>

    {{-- <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_transportfee[{{ $index }}]" label="Transport Fee" :value="old('load_transportfee.' . $index, $editing ? $load->load_transportfee : '')"
            maxlength="255" placeholder="Transport Fee" required></x-inputs.number>
    </x-inputs.group> --}}

    <x-inputs.group class="col-sm-4">
        <x-inputs.date name="load_transport_date[{{ $index }}]" label="Transport Date" :value="old('load_transport_date.' . $index, $editing ? $load->load_transport_date : '')"
            maxlength="255" placeholder="Transport Date" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.date name="load_delivery_date[{{ $index }}]" label="Delivery Date" :value="old('load_delivery_date.' . $index, $editing ? $load->load_delivery_date : '')"
            maxlength="255" placeholder="Delivery Date" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="loadpackagetype_id[{{ $index }}]" label="Load Type" required>
            @php $selected = old('loadpackagetype_id.' . $index, ($editing ? $load->loadpackagetype_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the load Type</option>
            @foreach ($loadpackagetypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="load_status[{{ $index }}]" label="Status" required>
            @php
                $statuses = ['instock', 'onmovement', 'delivery', 'closed'];
            @endphp
            @foreach ($statuses as $status)
                <option value="{{ $status }}"
                    {{ old('load_status.' . $index, $editing && $load->load_status == $status ? 'selected' : '') }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>


    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="unittype_id[{{ $index }}]" label="Unit Type" required>
            @php $selected = old('unittype_id.' . $index, ($editing ? $load->unittype_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Unit Type</option>
            @foreach ($unittypes as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4 weight-value-group" style="display: none !important">
        <x-inputs.number name="load_weight_value[{{ $index }}]" label="Unit Value" :value="old('load_weight_value.' . $index, $editing ? $load->load_weight_value : '')"
            maxlength="255" placeholder="Enter Unit Value" required disabled></x-inputs.number>
    </x-inputs.group>

    <div class="col-sm-4 mt-4">
        <button type="button" class="btn btn-danger remove-loadpackage-btn">
            Remove Load
        </button>
    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitTypeSelects = document.querySelectorAll('select[name^="unittype_id"]');

        unitTypeSelects.forEach(select => {
            const row = select.closest('.load-package-row');
            const weightInputGroup = row.querySelector('input[name^="load_weight_value"]').closest(
                '.col-sm-4');

            weightInputGroup.setAttribute('style', 'display: none !important');

            const weightInput = weightInputGroup.querySelector('input[name^="load_weight_value"]');
            weightInput.disabled = true;

            if (select.value && select.value !== '' && select.selectedIndex !== 0) {
                weightInputGroup.setAttribute('style', 'display: block !important');
                weightInput.disabled = false;
            }

            select.addEventListener('change', function(event) {
                const selectedValue = event.target.value;
                const isValidSelection = selectedValue && selectedValue !== '' && this
                    .selectedIndex !== 0;

                if (isValidSelection) {
                    weightInputGroup.setAttribute('style', 'display: block !important');
                    weightInput.disabled = false;
                    weightInput.focus();
                    weightInput.value = '';
                } else {
                    weightInputGroup.setAttribute('style', 'display: none !important');
                    weightInput.disabled = true;
                    weightInput.value = '';
                }

                console.log('Selected Unit Type ID:', selectedValue);
            });
        });
    });
</script>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all unit type select elements
        const unitTypeSelects = document.querySelectorAll('select[name^="unittype_id"]');

        // Add change event listener to each select element
        unitTypeSelects.forEach(select => {
            select.addEventListener('change', function(event) {
                const selectedValue = event.target.value;
                console.log('Selected Unit Type ID:', selectedValue);
                // You can also use alert if you prefer
                alert('Selected Unit Type ID: ' + selectedValue);
            });
        });
    });
</script> --}}
