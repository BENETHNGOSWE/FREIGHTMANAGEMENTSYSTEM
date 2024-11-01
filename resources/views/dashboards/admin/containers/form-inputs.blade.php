@php $editing = isset($container) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="container_number"
            label="Container Number"
            :value="old('container_number', ($editing ? $container->container_number : ''))"
            maxlength="255"
            placeholder="Container Number"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="container_size"
            label="Container Size"
            :value="old('container_size', ($editing ? $container->container_size : ''))"
            maxlength="255"
            placeholder="Container Size"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="container_volume"
            label="Container Volume"
            :value="old('container_volume', ($editing ? $container->container_volume : ''))"
            maxlength="255"
            placeholder="Container Volume"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.select name="container_status" label="Status" required>
            @php
                $statuses = ['portstation', 'onmovement', 'delivery', 'closed'];
            @endphp
            @foreach ($statuses as $status)
                <option value="{{ $status }}"
                    {{ old('container_status.', $editing && $container->container_status == $status ? 'selected' : '') }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
