@php $editing = isset($client) @endphp

<div class="row">
    <div class="{{$editing ? 'col-sm-12' : 'col-sm-6'}}">
        <div class="row mb-3">
            <div class="h3">
                <div class="text-uppercase">Client Information</div>
            </div>
        </div>
        <div class="row">
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_name" label="Client Name"
                    :value="old('client_name', $editing ? $client->client_name : '')" maxlength="255"
                    required></x-inputs.text>
            </x-inputs.group>
            
            <x-inputs.group class="col-sm-6">
                <x-inputs.select name="client_type_id" label="Client Type">
                    @php $selected = old('client_type_id', ($editing ? $client->client_type_id : '')) @endphp
                    <option disabled {{ empty($selected) ? 'selected' : '' }}>--Choose Client Type--</option>
                    @foreach ($clientTypes as $value => $label)
                        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </x-inputs.select>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_location" label="Client Location"
                    :value="old('client_location', $editing ? $client->client_location : '')" maxlength="255"
                    required></x-inputs.text>
            </x-inputs.group>
            
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_address_one" label="Client Address One"
                    :value="old('client_address_one', $editing ? $client->client_address_one : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_address_two" label="Client Address Two"
                    :value="old('client_address_two', $editing ? $client->client_address_two : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_tinnumber" label="Client TIN Number"
                    :value="old('client_tinnumber', $editing ? $client->client_tinnumber : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_phonenumber" label="Client Phone Number"
                    :value="old('client_phonenumber', $editing ? $client->client_phonenumber : '')" maxlength="255"
                    required></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_email" label="Client Email"
                    :value="old('client_email', $editing ? $client->client_email : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_city" label="Client City"
                    :value="old('client_city', $editing ? $client->client_city : '')" maxlength="255"
                    required></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_country" label="Client Country"
                    :value="old('client_country', $editing ? $client->client_country : '')" maxlength="255"
                    required></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_zipcode" label="Client Zipcode"
                    :value="old('client_zipcode', $editing ? $client->client_zipcode : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_fax" label="Client Fax"
                    :value="old('client_fax', $editing ? $client->client_fax : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="client_website_link" label="Client Website Link"
                    :value="old('client_website_link', $editing ? $client->client_website_link : '')" maxlength="255"></x-inputs.text>
            </x-inputs.group>
    
            <x-inputs.group class="col-sm-6">
                <x-inputs.file name="client_logo" label="Client Logo"></x-inputs.file>
            </x-inputs.group>
        </div>
    </div>
    
    @if(!$editing)
    <div class="col-sm-6">
        <div class="row mb-3">
            <div class="h3">
                <div class="text-uppercase">Register Company admin</div>
            </div>
        </div>
        <div class="row" style="background:#2222f31f !important;">
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="firstname" label="First name*" :value="old('firstname', $editing ? $client->firstname : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="middlename" label="Middle name*" :value="old('middlename', $editing ? $client->middlename : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="lastname" label="Sur name*" :value="old('lastname', $editing ? $client->lastname : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.email name="email" label="Email" :value="old('email', $editing ? $client->email : '')" maxlength="255"
                     required></x-inputs.email>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="password" label="Password" maxlength="255" required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="password_confirmation" label="Confirm Password" maxlength="255" required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="phone" label="Phone Number" :value="old('phone', $editing ? $client->phone : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            {{-- <x-inputs.group class="col-sm-6">
                <x-inputs.text name="address" label="Address" :value="old('address', $editing ? $client->address : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group> --}}


        </div>

    </div>
    @endif
</div>
