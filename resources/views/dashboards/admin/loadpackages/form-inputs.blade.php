@php $editing = isset($load) @endphp

<div class="row load-package-row">
    <!-- Checkbox for Ocean/Air Selection -->
    <x-inputs.group class="col-sm-12">
        {{-- <x-inputs.checkbox name="is_ocean[{{ $index }}]" label="Ocean Mode" :checked="old('is_ocean.' . $index, $editing && $load->is_ocean)" /> --}}
        <x-inputs.checkbox name="is_ocean[{{ $index }}]" label="Ocean Mode" :checked="old('is_ocean.' . $index, $editing && $load->is_ocean)" />
    </x-inputs.group>

    <!-- Load Name Field -->
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_name[{{ $index }}]" label="Load Name" :value="old('load_name.' . $index, $editing ? $load->load_name : '')" maxlength="255"
            placeholder="Load Name"></x-inputs.text>
    </x-inputs.group>

    <!-- Load Trucking Number Field -->
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="load_trucking_number[{{ $index }}]" label="Load Trucking Number" :value="old('load_trucking_number.' . $index, $editing ? $load->load_trking_number : '')"
            maxlength="255" placeholder="Trucking Number" required></x-inputs.text>
    </x-inputs.group>

    <!-- Load Price Field - hii inachukua caton ila in DB we defin as price to reduce changing errors -->
    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_price[{{ $index }}]" label="Number of Ctn(s)" :value="old('load_price.' . $index, $editing ? $load->load_price : '')" maxlength="255"
            placeholder="Number of Ctn(s)" required></x-inputs.number>
    </x-inputs.group>

    <!-- Description Field -->
    <x-inputs.group class="col-sm-4">
        <x-inputs.textarea name="load_description[{{ $index }}]" label="Description" :value="old('load_description.' . $index, $editing ? $load->load_description : '')"
            maxlength="255" placeholder="Description" required></x-inputs.textarea>
    </x-inputs.group>


    <!-- Transport Date Field -->
    <x-inputs.group class="col-sm-4">
        <x-inputs.date name="load_transport_date[{{ $index }}]" label="Transport Date" :value="old('load_transport_date.' . $index, $editing ? $load->load_transport_date : '')"
            placeholder="Transport Date" required></x-inputs.date>
    </x-inputs.group>

    <!-- Delivery Date Field -->
    <x-inputs.group class="col-sm-4">
        <x-inputs.date name="load_delivery_date[{{ $index }}]" label="Delivery Date" :value="old('load_delivery_date.' . $index, $editing ? $load->load_delivery_date : '')"
            placeholder="Delivery Date" required></x-inputs.date>
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

    <!-- Container Select Field (visible when Ocean is selected) -->
    <x-inputs.group class="col-sm-4 container-group" style="display: none;">
        <x-inputs.select name="container_id[{{ $index }}]" label="Container">
            @php $selected = old('container_id.' . $index, ($editing ? $load->container_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Container</option>
            @foreach ($containers as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <!-- Unit Type Select Field -->
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

    <!-- Weight Value Field (hidden by default, shown when unit type is selected) -->
    <x-inputs.group class="col-sm-4 weight-value-group" style="display: none !important">
        <x-inputs.number name="load_weight_value[{{ $index }}]" label="Unit Value" :value="old('load_weight_value.' . $index, $editing ? $load->load_weight_value : '')"
             min="0" max="10000" placeholder="Enter Unit Value (e.g., 0.0000067)" required step="any"
            disabled>
        </x-inputs.number>
    </x-inputs.group>

    


    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_length[{{ $index }}]" label="Load Length" :value="old('load_length.' . $index, $editing ? $load->load_length : '')"
            step="any" placeholder="Length" ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_width[{{ $index }}]" label="Load Width" :value="old('load_width.' . $index, $editing ? $load->load_width : '')"
            step="any" placeholder="Width" ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.number name="load_height[{{ $index }}]" label="Load Height" :value="old('load_height.' . $index, $editing ? $load->load_height : '')"
            step="any" placeholder="Height" ></x-inputs.number>
    </x-inputs.group>

    <!-- Remove Load Button -->
    {{-- <div class="col-sm-4 mt-4">
        <button type="button" class="btn btn-danger remove-loadpackage-btn">
            Remove Load
        </button>
    </div> --}}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mode selection logic to display container field based on mode selected
        const modeSelects = document.querySelectorAll('select[name^="mode"]');
        const oceanCheckboxes = document.querySelectorAll('input[name^="is_ocean"]');

        oceanCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('.load-package-row');
            const containerGroup = row.querySelector('.container-group');
            const containerSelect = containerGroup.querySelector('select');

            // Toggle the container field visibility based on checkbox
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    containerGroup.style.display = 'block';
                    containerSelect.required = true;
                } else {
                    containerGroup.style.display = 'none';
                    containerSelect.required = false;
                    containerSelect.value = ''; // Reset the container field
                }
            });

            // Initialize the checkbox state on page load
            if (checkbox.checked) {
                containerGroup.style.display = 'block';
                containerSelect.required = true;
            } else {
                containerGroup.style.display = 'none';
                containerSelect.required = false;
            }
        });

        // Handle Mode Selection for Ocean/Air toggle
        modeSelects.forEach(select => {
            const row = select.closest('.load-package-row');
            const containerGroup = row.querySelector('.container-group');
            const containerSelect = containerGroup.querySelector('select');

            // Toggle the container field visibility based on mode selection
            const toggleContainerField = () => {
                const selectedMode = select.value;
                if (selectedMode === 'ocean') {
                    containerGroup.style.display = 'block';
                    containerSelect.required = true;
                } else {
                    containerGroup.style.display = 'none';
                    containerSelect.required = false;
                    containerSelect.value = ''; // Reset the container field
                }
            };

            // Apply logic on page load
            toggleContainerField();

            // Add event listener for mode change
            select.addEventListener('change', toggleContainerField);
        });

        const transportDateInputs = document.querySelectorAll('input[name^="load_transport_date"]');
        const deliveryDateInputs = document.querySelectorAll('input[name^="load_delivery_date"]');

        deliveryDateInputs.forEach((deliveryDateInput, index) => {
            deliveryDateInput.addEventListener('change', function() {
                const transportDateInput = transportDateInputs[index];

                if (transportDateInput.value && this.value) {
                    const transportDate = new Date(transportDateInput.value);
                    const deliveryDate = new Date(this.value);

                    if (deliveryDate < transportDate) {
                        alert('Delivery date cannot be before transport date');
                        this.value = ''; // Clear the invalid date
                    }
                }
            });
        });

        // Handle Unit Type Selection Logic (for weight value visibility)
        const unitTypeSelects = document.querySelectorAll('select[name^="unittype_id"]');
        unitTypeSelects.forEach(select => {
            const row = select.closest('.load-package-row');
            const weightInputGroup = row.querySelector('.weight-value-group');
            const weightInput = weightInputGroup.querySelector('input[name^="load_weight_value"]');

            // Hide the weight value field initially
            weightInputGroup.style.display = 'none';
            weightInput.disabled = true;

            // Show the weight value field when a valid unit type is selected
            select.addEventListener('change', function(event) {
                const selectedValue = event.target.value;
                if (selectedValue) {
                    weightInputGroup.style.display = 'block';
                    weightInput.disabled = false;
                    weightInput.focus();
                    weightInput.value = '';
                } else {
                    weightInputGroup.style.display = 'none';
                    weightInput.disabled = true;
                    weightInput.value = '';
                }
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
