@php $editing = isset($unittype) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="unittype_name"
            label="unittype Number"
            :value="old('unittype_name', ($editing ? $unittype->unittype_name : ''))"
            maxlength="255"
            placeholder="Unit Type Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="unitytype_initial"
            label="unittype Size"
            :value="old('unitytype_initial', ($editing ? $unittype->unitytype_initial : ''))"
            maxlength="255"
            placeholder="Unit Type Initial"
        ></x-inputs.text>
    </x-inputs.group>
</div>
