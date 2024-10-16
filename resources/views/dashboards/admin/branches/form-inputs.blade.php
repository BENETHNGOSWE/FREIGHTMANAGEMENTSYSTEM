@php $editing = isset($branch) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="branch_name"
            label="Branch Name"
            :value="old('branch_name', ($editing ? $branch->branch_name : ''))"
            maxlength="255"
            placeholder="Branch Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="branch_code"
            label="Branch Code"
            :value="old('branch_code', ($editing ? $branch->branch_code : ''))"
            maxlength="255"
            placeholder="Branch Code"
        ></x-inputs.text>
    </x-inputs.group>
</div>
