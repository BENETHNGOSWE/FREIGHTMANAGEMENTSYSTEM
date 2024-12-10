@php $editing = isset($customercategory) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="categoryname"
            label="Category Name"
            :value="old('categoryname', ($editing ? $customercategory->categoryname : ''))"
            maxlength="255"
            placeholder="Category Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="categorydescription"
            label="Category Description"
            :value="old('categorydescription', ($editing ? $customercategory->categorydescription : ''))"
            maxlength="255"
            placeholder="Category Description"
        ></x-inputs.text>
    </x-inputs.group>
</div>
