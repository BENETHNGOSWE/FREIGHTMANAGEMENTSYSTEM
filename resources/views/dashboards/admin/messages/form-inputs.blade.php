@php $editing = isset($stafftype) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="staff_type_name"
            label="Staff TYpe Name"
            :value="old('staff_type_name', ($editing ? $stafftype->staff_type_name : ''))"
            maxlength="255"
            placeholder="Staff type Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="staff_type_description"
            label="Staff Type Description"
            :value="old('staff_type_description', ($editing ? $stafftype->staff_type_description : ''))"
            maxlength="255"
            placeholder="Staff Type Description"
        ></x-inputs.text>
    </x-inputs.group>
</div>
