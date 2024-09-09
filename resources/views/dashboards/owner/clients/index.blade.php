@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">clients</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('list.clients') }}">clients</a></span></p>
            </div>
            <div>
                <a href="{{ route('create.clients') }}" class="btn btn-primary me-3">+ Add new</a>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="table-responsive card-table">
                    <table id="clientsTable" class="display dataTablesCard white-border table-responsive-xl">
                        <thead>
                            <tr>
                                <th>
                                    s/n
                                </th>
                                <th class="text-left">
                                   
                                    @lang('crud.clients.inputs.client_logo')
                                </th>
                                <th class="text-left">
                                    Client Name
                                </th>
                                <th class="text-left">
                                    @lang('crud.clients.inputs.client_type_id')
                                </th>
                               
                                <th class="text-left">
                                    Admin name
                                </th>
                                <th class="text-left">
                                    Admin phone
                                </th>
                                <th class="text-left">
                                    @lang('crud.clients.inputs.client_city')
                                </th>
                                
                                <th class="text-center">
                                    @lang('crud.common.actions')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client_list as $client)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    
                                    <td>
                                        <img src="{{ asset('storage/uploads/' . $client->client_logo) }}" alt="client Logo"
                                            style="width: 50px; height: 50px; object-fit: cover;">

                                    </td>
                                    <td>{{ $client->client_name ?? '-' }}</td>
                                    <td>
                                        {{ $client->clientType() ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $client->admin() ? $client->admin()->name : '-'}}
                                    </td>
                                    <td>{{ $client->admin() ? $client->admin()->phone : '-' }}</td>
                                    <td>{{ $client->client_city ?? '-' }}</td>
                                    
                                    <td class="text-center" style="width: 134px;">
                                        <div role="group" aria-label="Row Actions" class="btn-group">
                                            @can('update', $client)
                                                <a href="#">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>
                                                @endcan @can('view', $client)
                                                <a href="#">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>
                                            @endcan

                                            @can('delete', $client)
                                                <form class="delete_form"
                                                    action="#"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger delete_button">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td  class="text-center py-5" colspan="15">
                                        @lang('crud.common.no_items_found')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function($) {
            var table = $("#clientsTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        })(jQuery);
    </script>

<script>
   const deleteButtons = document.querySelectorAll(".delete_button");

        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                swal({
                    title: "Are you sure to delete ?",
                    text: "You will not be able to recover deleted data !!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it !!",
                    cancelButtonText: "No, cancel it !!",
                    closeOnConfirm: !1,
                    closeOnCancel: !1
                }).then((result) => {
                    if (result.value) {
                        const form = button.closest(".delete_form");
                        form.submit();
                    } else {
                        swal("Cancelled", "Your data is safe", "success");
                    }
                });
            });
        });
    </script>
@endpush
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
