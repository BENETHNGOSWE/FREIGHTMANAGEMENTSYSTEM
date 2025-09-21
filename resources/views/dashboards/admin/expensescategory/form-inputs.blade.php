@php $editing = isset($officeexpense) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="name"
            label="Expense Category"
            :value="old('name', ($editing ? $officeexpense->name : ''))"
            maxlength="255"
            placeholder="Expense Category Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

</div>
