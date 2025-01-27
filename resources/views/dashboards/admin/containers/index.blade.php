@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">container</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">container
                            List</a></span>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.create.container') }}" class="btn btn-primary me-3">+ Add container</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.containers.index_title')</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        s/n
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.containers.inputs.container_number')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.containers.inputs.container_size')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.containers.inputs.container_volume')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.containers.inputs.container_status')
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($containers as $container)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>{{ strtoupper($container->container_number) ?? '-' }}</td>
                                        <td>{{ strtoupper($container->container_size) ?? '-' }}</td>
                                        <td>{{ strtoupper($container->container_volume) ?? '-' }}</td>
                                        <td>{!! return_status_badge($container->container_status) !!}</td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @can('update', $container)
                                                    <a href="{{ route('admin.edit.container', $container) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                    @endcan @can('view', $container)
                                                    <a href="{{ route('admin.show.container', $container) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </a>
                                                    @endcan @can('delete', $container)
                                                    <form class="delete_form"
                                                        action="{{ route('admin.delete.container', $container) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger delete_button mx-2">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                <button type="button" class="btn btn-primary load-container-btn"
                                                    data-container-id="{{ $container->id }}"
                                                    data-container-volume="{{ $container->container_volume }}">
                                                    Load Container
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="5">
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

        <div class="modal fade" id="loadContainerModal" tabindex="-1" role="dialog"
            aria-labelledby="loadContainerModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loadContainerModalLabel">Load Container</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="selected-container-id">
                        <table id="availableLoadsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all-loads">
                                    </th>
                                    <th>Load ID</th>
                                    <th>Load Volume</th>
                                    <th>Additional Details</th>
                                </tr>
                            </thead>
                            <tbody id="availableLoadsBody">
                                <!-- Loads will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="confirm-load-container">Load Selected
                            Loads</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        (function($) {
            var table = $("#customerTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        })(jQuery);
    </script>

    <script>
        $(document).ready(function() {
            // Delete button confirmation
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

            // Load Container button click event
            $('.load-container-btn').on('click', function() {
                var containerId = $(this).data('container-id');

                $.ajax({
                    url: `/containers/${containerId}/load`, // Ensure this URL matches your route definition
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Add CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Loads successfully added to container');
                            location.reload(); // Refresh page to show updated status
                        } else {
                            alert('Could not find optimal load combination');
                        }
                    },
                    error: function() {
                        alert('An error occurred while loading the container');
                    }
                });
            });

            // Initialize DataTable
            $("#loadtypeTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        });
    </script>
@endpush
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
