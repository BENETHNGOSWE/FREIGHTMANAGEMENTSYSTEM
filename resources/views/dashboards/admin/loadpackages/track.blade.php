@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Track Your Load</h2>
            </div>
            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.create.loadpackage') }}">Add
                            Load</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.loadtracking.loadpackage') }}" class="mt-4">
                    @csrf
                    <label for="load_no">Enter Load Number</label>
                    <input type="text" id="load_no" name="load_no" class="form-control" required><br>
                    <button class="btn btn-info">Track Now</button>
                </form><br>
                @if (isset($loadpackage))
                    <h3>Load Details</h3>
                    <table class="table table-bordered">
                        <tr>
                            <th>Load Owner:</th>
                            <td>
                                {{ $loadpackage->customer->customer_firstname }} 
                                {{ $loadpackage->customer->customer_middlename ?? '' }} 
                                {{ $loadpackage->customer->customer_lastname }}
                            </td>
                        </tr>
                        
                        <tr>
                            <th>Load Number:</th>
                            <td>{{ $loadpackage->load_no }}</td>
                        </tr>

                        <tr>
                            <th>Location:</th>
                            <td>{{ $loadpackage->load_trucking_number }}</td>
                        </tr>
                        <tr>
                            <th>Load Weight:</th>
                            <td>{{ $loadpackage->load_weight_value .' '.$loadpackage->unittype->unittype_name }}</td>
                        </tr>
                        <tr>
                            <th>Load Price:</th>
                            <td>$ {{ $loadpackage->load_price }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>{!! return_status_badge($loadpackage->container->container_status) !!}</td>
                        </tr>
                        
                        
                        <tr>
                            <th>Delivery Date:</th>
                            <td>{{ $loadpackage->load_delivery_date }}</td>
                        </tr>
                    </table>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
