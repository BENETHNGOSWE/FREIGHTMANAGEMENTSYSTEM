@php $editing = isset($customerlead) @endphp

<div class="row Kohler">
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
        <div class="form-group">
            <label>Categories</label>
            <div class="category-selector">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle category-button" id="categoriesButton">
                    Select Categories
                </button>
                
                <div class="category-dropdown-panel" id="categoryDropdownPanel" style="display: none;">
                    @php 
                        $selected = old('category_id', 
                            $editing ? $customerlead->customercategories->pluck('id')->toArray() : []
                        );
                        
                        // Calculate total number of categories and make two equal groups
                        $total = count($customercategories);
                        $halfPoint = ceil($total / 2);
                        $counter = 0;
                    @endphp
                    
                    <div class="category-columns">
                        <div class="left-col">
                            @foreach($customercategories as $value => $label)
                                @php $counter++; @endphp
                                @if($counter <= $halfPoint)
                                    <div class="category-item">
                                        <label>
                                            <input type="checkbox" name="category_id[]" value="{{ $value }}"
                                                {{ in_array($value, $selected) ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <div class="right-col">
                            @php $counter = 0; @endphp
                            @foreach($customercategories as $value => $label)
                                @php $counter++; @endphp
                                @if($counter > $halfPoint)
                                    <div class="category-item">
                                        <label>
                                            <input type="checkbox" name="category_id[]" value="{{ $value }}"
                                                {{ in_array($value, $selected) ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.email name="customer_email" label="Email" :value="old('email', $editing ? $customerlead->customer_email : '')" maxlength="255" placeholder="Email"
            ></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $customerlead->phone : '')" maxlength="15" placeholder="Phone" required
            type="tel" pattern="^\255[0-9]{9}$" inputmode="numeric" title="Phone number must start with 255 and be followed by 9 digits.">
        </x-inputs.text>
    </x-inputs.group>
</div>

@push('styles')
<style>
    .category-selector {
        position: relative;
        width: 100%;
    }
    
    .category-button {
        width: 100%;
        text-align: left;
        position: relative;
    }
    
    .category-dropdown-panel {
        position: absolute;
        width: 100%;
        min-width: 350px;
        padding: 15px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 1000;
        margin-top: 5px;
        max-height: 350px;
        overflow-y: auto;
    }
    
    .category-columns {
        display: flex;
        width: 100%;
    }
    
    .category-columns .left-col,
    .category-columns .right-col {
        flex: 0 0 50%; /* Fixed width for both columns */
        padding: 0 5px;
        box-sizing: border-box;
    }
    
    .category-columns .left-col {
        border-right: 1px solid #eee;
    }
    
    .category-item {
        padding: 5px 0;
    }
    
    .category-item label {
        display: flex;
        align-items: center;
        margin: 0;
        cursor: pointer;
    }
    
    .category-item input[type="checkbox"] {
        margin-right: 8px;
    }
    
    .category-item:hover {
        background-color: #f5f5f5;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('categoriesButton');
        const panel = document.getElementById('categoryDropdownPanel');
        
        // Make sure the panel is hidden by default
        panel.style.display = 'none';
        
        // Toggle dropdown on button click
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!button.contains(event.target) && !panel.contains(event.target)) {
                panel.style.display = 'none';
            }
        });
        
        // Update button text with selected categories
        const checkboxes = panel.querySelectorAll('input[type="checkbox"]');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateButtonText();
            });
        });
        
        function updateButtonText() {
            const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            button.textContent = selectedCount > 0 
                ? `${selectedCount} Categories Selected` 
                : 'Select Categories';
        }
        
        // Set initial button text based on selected checkboxes
        @if($editing && isset($customerlead) && $customerlead->customercategories->isNotEmpty())
            button.textContent = '{{ $customerlead->customercategories->count() }} Categories Selected';
        @else
            updateButtonText();
        @endif
    });
</script>
@endpush