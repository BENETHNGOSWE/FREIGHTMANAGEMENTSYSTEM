@php $editing = isset($officeexpense) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="expenses_description"
            label="Expense Description"
            :value="old('expenses_description', ($editing ? $officeexpense->expenses_description : ''))"
            maxlength="255"
            placeholder="Expense Description"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="expenses_cost"
            label="Expenses Amount Cost"
            :value="old('expenses_cost', ($editing ? $officeexpense->expenses_cost : ''))"
            maxlength="255"
            placeholder="Expenses Amount Cost"
        ></x-inputs.text>
    </x-inputs.group>

  <x-inputs.group class="col-sm-4 container-group">
    <x-inputs.select name="office_expense_categories_id" label="Expenses Category">
        @php 
            $selected = old('office_expense_categories_id', ($editing ? $officeexpense->office_expense_categories_id : '')) 
        @endphp
        <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Category</option>
        @foreach ($categories_expenses as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </x-inputs.select>
</x-inputs.group>


    <x-inputs.group class="col-sm-6">
        <x-inputs.select name="status" label="Status" disabled>
            <option value="0" >Pending</option>
            <option value="1" >Approved</option>
            <option value="2" >Not Approved</option>
        </x-inputs.select>
    </x-inputs.group>
</div>

