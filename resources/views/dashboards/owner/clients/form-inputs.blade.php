@php $editing = isset($hospital) @endphp

<div class="row">
    <div class="{{$editing ? 'col-sm-12' : 'col-sm-6'}}">
        <div class="row mb-3">
            <div class="h3">
                <div class="text-uppercase">Hospital Information</div>
            </div>
        </div>
        <div class="row">
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_name" label="Hospital Name" :value="old('hospital_name', $editing ? $hospital->hospital_name : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>
            <x-inputs.group class="col-sm-6">
                <x-inputs.select name="hospital_type_id" label="Hospital Type">
                    @php $selected = old('hospital_type_id', ($editing ? $hospital->hospital_type_id : '')) @endphp
                    <option disabled {{ empty($selected) ? 'selected' : '' }}>--Choose Hospital Type--</option>
                    @foreach ($hospitalTypes as $value => $label)
                        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </x-inputs.select>
            </x-inputs.group>
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_location" label="Hospital Location" :value="old('hospital_location', $editing ? $hospital->hospital_location : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_address_one" label="Hospital Address One" :value="old('hospital_address_one', $editing ? $hospital->hospital_address_one : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_address_two" label="Hospital Address Two" :value="old('hospital_address_two', $editing ? $hospital->hospital_address_two : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_tinnumber" label="Owner Busness TinNumber (if applicable)" :value="old('hospital_tinnumber', $editing ? $hospital->hospital_tinnumber : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_phonenumber" label="Hospital Phonenumber" :value="old('hospital_phonenumber', $editing ? $hospital->hospital_phonenumber : '')" maxlength="255"
                    required></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_email" label="Hospital Email" :value="old('hospital_email', $editing ? $hospital->hospital_email : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_city" label="Hospital City" :value="old('hospital_city', $editing ? $hospital->hospital_city : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.select name="hospital_country" label="Hospital Country" :value="old('hospital_country', $editing ? $hospital->hospital_country : '')"
                    required>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
        
                </x-inputs.select>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_zipcode" label="Hospital Zipcode" :value="old('hospital_zipcode', $editing ? $hospital->hospital_zipcode : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_fax" label="Hospital Fax" :value="old('hospital_fax', $editing ? $hospital->hospital_fax : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="hospital_website_link" label="Hospital Website Link" :value="old('hospital_website_link', $editing ? $hospital->hospital_website_link : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group>
        
            
            <x-inputs.group class="col-sm-6">
                <x-inputs.file name="hospital_logo" label="Hospital Logo" :value="old('hospital_logo', $editing ? $hospital->hospital_logo : '')"></x-inputs.file>
            </x-inputs.group>
        </div>

    </div>
    @if(!$editing)
    <div class="col-sm-6">
        <div class="row mb-3">
            <div class="h3">
                <div class="text-uppercase">Register hospital admin</div>
            </div>
        </div>
        <div class="row" style="background:#2222f31f !important;">
            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="firstname" label="First name*" :value="old('firstname', $editing ? $hospital->firstname : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="middlename" label="Middle name*" :value="old('middlename', $editing ? $hospital->middlename : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="lastname" label="Sur name*" :value="old('lastname', $editing ? $hospital->lastname : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.email name="email" label="Email" :value="old('email', $editing ? $hospital->email : '')" maxlength="255"
                     required></x-inputs.email>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="password" label="Password" maxlength="255" required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="password_confirmation" label="Confirm Password" maxlength="255" required></x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-6">
                <x-inputs.text name="phone" label="Phone Number" :value="old('phone', $editing ? $hospital->phone : '')" maxlength="255"
                     required></x-inputs.text>
            </x-inputs.group>

            {{-- <x-inputs.group class="col-sm-6">
                <x-inputs.text name="address" label="Address" :value="old('address', $editing ? $hospital->address : '')" maxlength="255"
                    ></x-inputs.text>
            </x-inputs.group> --}}


        </div>

    </div>
    @endif
</div>
