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
                <h4 class="card-title">{{ $loadpackage->load_name ?? '-' }}</h4>
            </div>
            <div class="card-body">

                <div class="col-xl-12">

                    <!-- Nav tabs -->
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#load_info" aria-selected="true"
                                    role="tab"><i class="la la-home me-2"></i>load Details</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#admin_details" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-user me-2"></i> Cost Details</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#invoices" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-dollar me-2"></i> Invoices </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="load_info" role="tabpanel">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-responsive-sm">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%">load Name</th>
                                                            <th>{{ $loadpackage->load_no ?? '-' }}</th>
                                                        </tr>


                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>load Type</td>
                                                            <td>{{ $loadpackage->Loadpackagetype->load_type_name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Name</td>
                                                            <td>{{ $loadpackage->load_name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Location</td>
                                                            <td>{{ $loadpackage->load_location ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Taransport Fee</td>
                                                            <td>{{ $loadpackage->load_transportfee ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Transort Date</td>
                                                            <td>{{ $loadpackage->load_transport_date ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Derivery Date</td>
                                                            <td>{{ $loadpackage->load_delivery_date ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Status</td>
                                                            <td>{{ $loadpackage->load_status ?? '-' }}</td>
                                                        </tr>
                                                       
                                                        <tr>
                                                            <td>Comment</td>
                                                            <td>{{ $loadpackage->comment_operator ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Kg</td>
                                                            <td>{{ $loadpackage->load_weight_kg ?? '-' }}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>Descrption</td>
                                                            <td>{{ $loadpackage->load_description ?? '-' }}</td>
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
                                    <div class="col-sm-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-responsive-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%" class="text-capitalize">Admin Name</th>
                                                        <th>{{ $admin->user->name ?? '-' }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Admin Email</td>
                                                        <td>{{ $admin->email ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Admin Phone</td>
                                                        <td>{{ $admin->phone ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Admin Address</td>
                                                        <td>{{ $admin->address ?? '-' }}</td>
                                                    </tr>
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
                            <div class="tab-pane fade" id="subscriptions" role="tabpanel">
                                <div class="pt-4">
                                    <h4>subscriptions</h4>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="message" role="tabpanel">
                                <div class="pt-4">
                                    <h4>This is message title</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex ">
                    <a href="{{ route('admin.edit.loadpackage', $loadpackage) }}" class="btn btn-outline-success">
                        <i class="icon fa fa-add"></i>
                        Edit
                    </a>

                    @can('delete', $loadpackage)
                        <form class="delete_form mx-3" action="#"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger delete_button">
                                <i class="fas fa-trash"></i>delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
