@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Edit Customer Lead</h2>
            </div>
            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Edit</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <x-form method="PUT" action="{{ route('admin.update.customerlead', $customerlead) }}" has-files class="mt-4">
                    <div class="row">
                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="customer_firstname" label="First Name" :value="old('customer_firstname', $customerlead->customer_firstname)" maxlength="255" placeholder="First name" required />
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="customer_middlename" label="Middle Name" :value="old('customer_middlename', $customerlead->customer_middlename)" maxlength="255" placeholder="Middle name" />
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="customer_lastname" label="Last Name" :value="old('customer_lastname', $customerlead->customer_lastname)" maxlength="255" placeholder="Last name" required />
                        </x-inputs.group>

                 

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.email name="customer_email" label="Email" :value="old('customer_email', $customerlead->customer_email)" maxlength="255" placeholder="Email" required />
                        </x-inputs.group>

                       

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $customerlead->phone : '')" maxlength="15" placeholder="Phone" required
                                type="tel" pattern="^\255[0-9]{9}$" inputmode="numeric" title="Phone number must start with 255 and be followed by 9 digits.">
                            </x-inputs.text>
                        </x-inputs.group>

                   

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.select name="category_id" label="Category" required>
                                @php $selected = old('category_id', $customerlead->category_id) @endphp
                                <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Category</option>
                                @foreach ($customercategories as $value => $label)
                                    <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </x-inputs.select>
                        </x-inputs.group>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.list.customerlead') }}" class="btn btn-light">
                            <i class="fa-solid fa-backward"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
