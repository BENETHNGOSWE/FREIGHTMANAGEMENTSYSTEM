@php $editing = isset($godownaddress) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="godown_location"
            label="Godown Location"
            :value="old('godown_location', ($editing ? $godownaddress->godown_location : ''))"
            maxlength="255"
            placeholder="Godown Location"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="godown_address_one"
            label="Godown Address"
            :value="old('godown_address_one', ($editing ? $godownaddress->godown_address_one : ''))"
            maxlength="255"
            placeholder="Godown Address"
        ></x-inputs.text>
    </x-inputs.group>

     <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="godown_address_two"
            label="Godown Address 2"
            :value="old('godown_address_two', ($editing ? $godownaddress->godown_address_two : ''))"
            maxlength="255"
            placeholder="Godown Address 2"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="godown_phonenumber"
            label="Godown PhoneNumber"
            :value="old('godown_phonenumber', ($editing ? $godownaddress->godown_phonenumber : ''))"
            maxlength="255"
            placeholder="Godown PhoneNumber"
        ></x-inputs.text>
    </x-inputs.group>
</div>
