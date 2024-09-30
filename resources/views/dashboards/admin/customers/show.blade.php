@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Loads</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Load
                            Show</a></span></p>
            </div>

        </div>
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $customer->customer_firstname ?? '-' }}
                        {{ $customer->customer_lastname ?? '-' }}</h4>
                </div>
                <div class="card-body">
                    <div class="col-xl-12">
                        <!-- Nav tabs -->
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#customer_info"
                                        aria-selected="true" role="tab"><i class="la la-home me-2"></i>Customer
                                        Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#admin_details" aria-selected="false"
                                        role="tab" tabindex="-1"><i class="la la-user me-2"></i> Customer Load Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#invoices" aria-selected="false"
                                        role="tab" tabindex="-1"><i class="la la-dollar me-2"></i> Invoices </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="customer_info" role="tabpanel">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-responsive-sm">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 20%">First Name</th>
                                                                <th>{{ $customer->customer_firstname ?? '-' }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Middle Name</td>
                                                                <td>{{ $customer->customer_middlename ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Last Name</td>
                                                                <td>{{ $customer->customer_lastname ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email</td>
                                                                <td>{{ $customer->customer_email ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phone</td>
                                                                <td>{{ $customer->phone ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Gender</td>
                                                                <td>{{ ucfirst($customer->customer_gender) ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Address</td>
                                                                <td>{{ $customer->customer_address ?? '-' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="admin_details" role="tabpanel">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-responsive-sm">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 20%" class="text-capitalize">Customer Load
                                                                </th>
                                                                <th>{{ $customer->customer_firstname ?? '-'  }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($customer->loadpackage as $load)
                                                                <tr>
                                                                    <td style="color:red; font-weight:bold;">Load No</td>
                                                                    <td>{{ $load->load_no ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Load Type</td>
                                                                    <td>{{ $load->loadpackagetype->load_type_name ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Location</td>
                                                                    <td>{{ $load->load_location ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Transport Fee</td>
                                                                    <td>{{ $load->load_transportfee ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Transport Date</td>
                                                                    <td>{{ $load->load_transport_date ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Delivery Date</td>
                                                                    <td>{{ $load->load_delivery_date ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Status</td>
                                                                    <td>{{ $load->load_status ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Comments</td>
                                                                    <td>{{ $load->comment_operator ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Weight (Kg)</td>
                                                                    <td>{{ $load->load_weight_kg ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Description</td>
                                                                    <td>{{ $load->load_description ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                            
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="invoices" role="tabpanel">
                                    <div class="pt-4">
                                        <h4>Invoices</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex">
                        <a href="{{ route('admin.edit.customer', $customer) }}" class="btn btn-outline-success">
                            <i class="icon fa fa-add"></i>
                            Edit
                        </a>

                        @can('delete', $customer)
                            <form class="delete_form mx-3"
                                action="{{ route('admin.delete.customer', ['customer' => $customer->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger delete_button">
                                    <i class="fas fa-trash"></i>Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
