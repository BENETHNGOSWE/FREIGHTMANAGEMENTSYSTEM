@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Load Types</h2>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Load type
                        Show</a></span></p>
        </div>

    </div>
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $loadpackagetype->load_type_name ?? '-' }}</h4>
            </div>
            <div class="card-body">

                <div class="col-xl-12">

                    <!-- Nav tabs -->
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#load_info" aria-selected="true"
                                    role="tab"><i class="la la-home me-2"></i>load Type Details</a>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#admin_details" aria-selected="false"
                                    role="tab" tabindex="-1"><i class="la la-user me-2"></i> Cost Details</a>
                            </li> --}}
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
                                                            <th style="width: 20%">load Type Name</th>
                                                            <th>{{ $loadpackagetype->load_type_name ?? '-' }}</th>
                                                        </tr>


                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Description</td>
                                                            <td>{{ $loadpackagetype->load_type_description ?? '-' }}</td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex ">
                    <a href="{{ route('admin.edit.loadpackagetpye', $loadpackagetype) }}" class="btn btn-outline-success">
                        <i class="icon fa fa-add"></i>
                        Edit
                    </a>

                    @can('delete', $loadpackagetype)
                        <form class="delete_form mx-3" action="{{ route('admin.delete.loadpackagetpye', $loadpackagetype)}}"
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
