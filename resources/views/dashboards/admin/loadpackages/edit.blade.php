@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit load</h2>
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('admin.list.loadpackage') }}">Edit load</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('admin.update.loadpackage', $loadpackage) }}"
                class="mt-4"
            >
          
            <div class="row">
                <!-- First row with two fields -->
               
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_name">Load Name</label>
                        <input type="text" value="{{ $loadpackage->load_name }}" name="load_name" class="form-control" placeholder="Load Name">
                        @error('load_name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Second row with two fields -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_trucking_number">Load Location</label>
                        <input type="text" value="{{ $loadpackage->load_trucking_number }}" name="load_trucking_number" class="form-control" placeholder="Load Location">
                        @error('load_trucking_number') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Dropdown for Load Type -->
                    <div class="form-group">
                        <label for="loadpackagetype_id">Load Type</label>
                        <select name="loadpackagetype_id" class="form-control" required>
                            <option disabled {{ empty($loadpackage->loadpackagetype_id) ? 'selected' : '' }}>Please select the load Type</option>
                            @foreach ($loadpackagetypes as $value => $label)
                                <option value="{{ $value }}" {{ $loadpackage->loadpackagetype_id == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('loadpackagetype_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
               
            </div>
            
            <div class="row">
                <!-- Third row with two fields -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_price">Load Price</label>
                        <input type="text" value="{{ $loadpackage->load_price }}" name="load_price" class="form-control" placeholder="Load Price">
                        @error('load_price') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_transportfee">Transport Fee</label>
                        <input type="text" value="{{ $loadpackage->load_transportfee }}" name="load_transportfee" class="form-control" placeholder="Transport Fee">
                        @error('load_transportfee') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Fourth row with two fields -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_transport_date">Transport Date</label>
                        <input type="date" value="{{ $loadpackage->load_transport_date }}" name="load_transport_date" class="form-control" placeholder="Transport Date">
                        @error('load_transport_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_delivery_date">Delivery Date</label>
                        <input type="date" value="{{ $loadpackage->load_delivery_date }}" name="load_delivery_date" class="form-control" placeholder="Delivery Date">
                        @error('load_delivery_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Fifth row with two fields -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_status">Status</label>
                        <input type="text" value="{{ $loadpackage->load_status }}" name="load_status" class="form-control" placeholder="Status">
                        @error('load_status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_weight_kg">Load Kg</label>
                        <input type="number" value="{{ $loadpackage->load_weight_kg }}" name="load_weight_kg" class="form-control" placeholder="Load Kg">
                        @error('load_weight_kg') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
               
            </div>
            
            <div class="row">
                <!-- Sixth row with two fields -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="comment_operator">Operator Comment</label>
                        <textarea class="form-control" name="comment_operator" rows="3">{{ $loadpackage->comment_operator }}</textarea>
                        {{-- <input type="text" value="{{ $loadpackage->comment_operator }}" name="comment_operator" class="form-control" placeholder="Operator Comment"> --}}
                        @error('comment_operator') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="load_description">Description</label>
                        <textarea class="form-control" name="load_description" rows="3">{{ $loadpackage->load_description }}</textarea>
                        @error('load_description') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
              
            </div>
            

            <div class="mt-4">
                <a href="{{ route('admin.list.loadpackage') }}" class="btn btn-light">
                    <i class="fa-solid fa-backload"></i> @lang('crud.common.back')
                </a>

                <button type="submit" class="btn btn-primary float-right">
                    <i class="icon ion-md-save"></i> @lang('crud.common.update')
                </button>
            </div>

            </x-form>
        </div>
    </div>
</div>
@endsection
