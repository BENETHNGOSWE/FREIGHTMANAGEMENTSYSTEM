@php $editing = isset($client) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $client->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="logo"
            label="Logo"
            :value="old('logo', ($editing ? $client->logo : ''))"
            maxlength="255"
            placeholder="Logo"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="address"
            label="Address"
            :value="old('address', ($editing ? $client->address : ''))"
            maxlength="255"
            placeholder="Address"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="country_id" label="Country" required>
            @php $selected = old('country_id', ($editing ? $client->country_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Country</option>
            @foreach($countries as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="city"
            label="City"
            :value="old('city', ($editing ? $client->city : ''))"
            maxlength="255"
            placeholder="City"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="registration_number"
            label="Registration Number"
            :value="old('registration_number', ($editing ? $client->registration_number : ''))"
            maxlength="255"
            placeholder="Registration Number"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="subdomain"
            label="Subdomain"
            :value="old('subdomain', ($editing ? $client->subdomain : ''))"
            maxlength="255"
            placeholder="Subdomain"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="client_type_id" label="Client Type" required>
            @php $selected = old('client_type_id', ($editing ? $client->client_type_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client Type</option>
            @foreach($clientTypes as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
