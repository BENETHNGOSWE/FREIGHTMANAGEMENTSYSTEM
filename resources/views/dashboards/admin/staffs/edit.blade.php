@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Edit</h2>

            </div>

            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Edit</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('admin.update.staff', $staff) }}" has-files class="mt-4">
                    <div class="row">
                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="staff_firstname" label="First Name" :value="old('firstname', $editing ? $staff->staff_firstname : '')" maxlength="255"
                                placeholder="First name" required></x-inputs.text>
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="staff_middlename" label="Middle Name" :value="old('middlename', $editing ? $staff->staff_middlename : '')" maxlength="255"
                                placeholder="Middle name"></x-inputs.text>
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="staff_lastname" label="Last Name" :value="old('lastname', $editing ? $staff->staff_lastname : '')" maxlength="255"
                                placeholder="Last name" required></x-inputs.text>
                        </x-inputs.group>


                        <x-inputs.group class="col-sm-4">
                            <x-inputs.select name="staff_gender" label="Staff Gender">
                                @php $selected = old('staff_gender', ($editing ? $staff->staff_gender : '')) @endphp
                                <option value="male" {{ $selected == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $selected == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="others" {{ $selected == 'others' ? 'selected' : '' }}>Others</option>
                            </x-inputs.select>
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.email name="staff_email" label="Email" :value="old('email', $editing ? $staff->staff_email : '')" maxlength="255"
                                placeholder="Email" required></x-inputs.email>
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="phone" label="Phone" :value="old('phone', $editing ? $staff->phone : '')" maxlength="255"
                                placeholder="Phone" required type="tel" pattern="^\+255[0-9]{9}$" inputmode="numeric"
                                title="Phone number must start with +255 and be followed by 9 digits."></x-inputs.text>
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.text name="staff_address" label="Staff Address" :value="old('staff_address', $editing ? $staff->staff_address : '')" maxlength="255"
                                placeholder="Staff Address"></x-inputs.text>
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.select name="branch_id" label="Branch" disabled>
                                <option value="{{ $staff->branch_id }}" selected>{{ $branches[$staff->branch_id] }}</option>
                            </x-inputs.select>
                            <input type="hidden" name="branch_id" value="{{ $staff->branch_id }}">
                        </x-inputs.group>

                        <x-inputs.group class="col-sm-4">
                            <x-inputs.select name="staff_type_id" label="Staff Type" >
                                @php $selected = old('staff_type_id', ($editing ? $staff->staff_type_id : '')) @endphp
                                <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Staff Type
                                </option>
                                @foreach ($staffTypes as $value => $label)
                                    <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </x-inputs.select>
                        </x-inputs.group>



                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.list.staff') }}" class="btn btn-light">
                            <i class="fa-solid fa-backward"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.create')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection