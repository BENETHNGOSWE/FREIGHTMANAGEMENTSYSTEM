@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Ward</h2>
            
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('dashboard.index') }}">Edit Ward</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="POST"
                action="{{ route('update.wards',$ward) }}"
                class="mt-4"
            >
          
            <div class="row">
                <div class="row">
            
                    <div class="col-sm 6">
                    <div class="form-group">
                        <label for="">Ward No</label>
                        <input type="text" value="{{$ward->ward_no}}"  name="ward_no" id="" class="form-control" placeholder="" aria-describedby="helpId">
                        <small id="helpId" class="text-muted">
                            @error('ward_no')
                                 <div class="text-danger">{{ $message }}</div>
                            @enderror
            
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="">Ward Name</label>
                        <input type="text"  value="{{$ward->ward_name}}" name="ward_name" id="" class="form-control" placeholder=""
                            aria-describedby="helpId">
                        <small id="helpId" class="text-muted">
                            @error('ward_name')
                                 <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </small>
                    </div>
            
                    <div class="form-group">
                        <label for="">Ward location</label>
                        <input type="text"  value="{{$ward->ward_location}}" name="ward_location" id="" class="form-control" placeholder=""
                            aria-describedby="helpId">
                        <small id="helpId" class="text-muted">
                            @error('ward_location')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </small>
                    </div>
            
                    <div class="form-group">
                        <label for="my-textarea">Description</label>
                        <textarea id="my-textarea"  value="{{$ward->ward_description}}" class="form-control" name="ward_description" rows="3"></textarea>
                        <small id="helpId" class="text-muted">
                            @error('ward_description')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </small>
                    </div>
            
            
                    @foreach ($wardtypes as $wardtype)
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="ward_type_id[]" id=""
                                    value="{{ $wardtype->id }}" >
            
                            </label>
                        </div>
                        {{ $wardtype->ward_type_name }} <br>
                        <small id="helpId" class="text-muted">
                            @error('ward_type_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </small>
                    @endforeach
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
                    {{-- 
                        <x-inputs.group class="col-sm-9">
                            <x-inputs.text
                                name="ward_name"
                                label="Ward Name"
                                :value="old('ward_name', ($editing ? $ward->ward_name : ''))"
                                maxlength="255"
                                placeholder="Ward Name"
                                required
                            ></x-inputs.text>
                        </x-inputs.group>
                        
                        <x-inputs.group class="col-sm-9">
                            <x-inputs.text
                                name="ward_no"
                                label="Ward No"
                                :value="old('ward_no', ($editing ? $ward->ward_no : ''))"
                                maxlength="255"
                                placeholder="Ward No"
                                required
                            ></x-inputs.text>
                        </x-inputs.group>
            
                        
            
                        <x-inputs.group class="col-sm-9">
                            <x-inputs.text
                                name="ward_description"
                                label="Ward Description"
                                :value="old('diagnosis_description', ($editing ? $ward->diagnosis_description : ''))"
                                maxlength="255"
                                placeholder="Diagnosis Description"
                                
                            ></x-inputs.text>
                        </x-inputs.group>
            
                        <x-inputs.group class="col-sm-9">
                            <x-inputs.text
                                name="ward_location"
                                label="Ward Location"
                                :value="old('ward_location', ($editing ? $ward->ward_location : ''))"
                                maxlength="255"
                                placeholder="Ward Location"
                                required
                            ></x-inputs.text>
                        </x-inputs.group>
                    </div>
                    <div class="col-sm-6">
                        <div class="title">
                            <h2 class="h4">Choose Ward Type  For this ward </h2>
                        </div>
            
                        @foreach ($wardtypes as $wardtype)
                            <div class="col-sm-3">
                                <x-inputs.checkbox id="ward_type_id{{ $wardtype->id }}" name="ward_types_id[]"
                                    label="{{ strtoupper($wardtype->ward_type_name) }}" value="{{ $wardtype->id }}" :checked="isset($ward) ? $ward->wardtypes->contains($wardtype->id) : false"
                                    :add-hidden-value="false"></x-inputs.checkbox>
                            </div>
                        @endforeach --}}
                      
                    </div> 
                </div>
            </div>

                <div class="mt-4">
                    <a
                        href="{{ route('list.wards') }}"
                        class="btn btn-light"
                    >
                        <i class="fa-solid fa-backward"></i>
                        @lang('crud.common.back')
                    </a>

                    <a
                        href="{{ route('create.wards') }}"
                        class="btn btn-light"
                    >
                        
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
