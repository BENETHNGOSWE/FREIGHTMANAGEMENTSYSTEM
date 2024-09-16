@php $editing = isset($loadpackagetype) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="load_type_name"
            label="load Type Name"
            :value="old('load_type_name', ($editing ? $loadpackagetype->load_type_name : ''))"
            maxlength="255"
            placeholder="Load type Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="load_type_description"
            label="Load Type Description"
            :value="old('load_type_description', ($editing ? $loadpackagetype->load_type_description : ''))"
            maxlength="255"
            placeholder="Load Type Description"
        ></x-inputs.text>
    </x-inputs.group>
</div>
