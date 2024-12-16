@php $editing = isset($sendername) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="name"
            label="Sender Name"
            :value="old('name', ($editing ? $sendername->name : ''))"
            maxlength="255"
            placeholder="Sender Name"
            required
        ></x-inputs.text>
    </x-inputs.group>
</div>
