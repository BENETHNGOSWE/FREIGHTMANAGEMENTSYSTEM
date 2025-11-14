@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Container</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Container List</a></span></p>
            </div>
            <div>
                <a href="{{ route('admin.create.container') }}" class="btn btn-primary me-3">+ Add Container</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        
                        {{-- Removed the old global progress bar container --}}

                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.containers.index_title')</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>s/n</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_number')</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_size')</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_volume')</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_status')</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($containers as $container)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper($container->container_number) ?? '-' }}</td>
                                        <td>{{ strtoupper($container->container_size) ?? '-' }}</td>
                                        <td>{{ strtoupper($container->container_volume) ?? '-' }}</td>
                                        <td>{!! return_status_badge($container->container_status) !!}</td>
                                        <td class="text-center" style="width: 250px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @can('update', $container)
                                                    <a href="{{ route('admin.edit.container', $container) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                @endcan
                                                @can('view', $container)
                                                    <a href="{{ route('admin.show.container', $container) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </a>
                                                @endcan
                                                @can('delete', $container)
                                                    <form class="delete_form" action="{{ route('admin.delete.container', $container) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger delete_button me-2">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                
                                                {{-- NEW MODAL TRIGGER BUTTON --}}
                                                <button 
                                                    type="button" 
                                                    class="btn btn-primary load-container-trigger me-2" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#loadPackagesModal"
                                                    data-container-id="{{ $container->id }}"
                                                    data-container-number="{{ $container->container_number }}"
                                                >
                                                    <i class="fas fa-truck-loading"></i> Load
                                                </button>

                                                <a href="{{ route('admin.loadlistpdf.container', $container) }}" class="btn btn-secondary print-button">
                                                    <i class="fas fa-print"></i> Print
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="6">
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
    </div>

    {{-- 2. BOOTSTRAP MODAL STRUCTURE --}}
    <div class="modal fade" id="loadPackagesModal" tabindex="-1" aria-labelledby="loadPackagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loadPackagesModalLabel">Load Packages to Container</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    {{-- Loading spinner displayed until AJAX content is injected --}}
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                        <p class="mt-2">Loading available packages...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="modal-load-selected" class="btn btn-primary">
                        <i class="fas fa-truck-loading"></i> Load Selected Packages
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- CSS for the modal's internal progress bar --}}
    <style>
        #modal-progress-bar-container {
            width: 100%;
            background-color: #ddd;
            margin-bottom: 15px;
        }
        #modal-progress-bar {
            width: 1%;
            height: 30px;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 30px;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Keep existing delete logic
            const deleteButtons = document.querySelectorAll(".delete_button");
            deleteButtons.forEach(function(button) {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    swal({
                        title: "Are you sure to delete?",
                        text: "You will not be able to recover deleted data !!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it !!",
                        cancelButtonText: "No, cancel it !!",
                        closeOnConfirm: false,
                        closeOnCancel: false
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

            // Keep existing DataTable logic
            $('#loadtypeTable').DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });

            // 1. Handle Modal Trigger Click: Load content via AJAX GET
            $('.load-container-trigger').on('click', function() {
                let containerId = $(this).data('container-id');
                let containerNumber = $(this).data('container-number');
                let modalBody = $('#modal-body-content');

                // Update modal title
                $('#loadPackagesModalLabel').text(`Load Packages to Container ${containerNumber}`);
                
                // Reset/Show initial loading spinner
                modalBody.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading available packages...</p></div>');
                
                // Show footer buttons in case they were hidden by a previous submission attempt
                $('#modal-load-selected, #loadPackagesModal .btn-secondary').show();

                // AJAX GET to fetch the table content from the controller
                $.ajax({
                    url: `/container/${containerId}/loadpackages`, // Ensure this route exists and points to your showloadstoload method
                    type: 'GET',
                    success: function(html) {
                        modalBody.html(html);
                        
                        // Re-attach select-all logic to the new content
                        $('#modal-body-content #select-all').off('click').on('click', function() {
                            $('#modal-body-content .package-checkbox').prop('checked', this.checked);
                        });

                        // Make sure the internal data table is initialized if using one inside the modal
                        // $('#modal-body-content #loadTable').DataTable({...});
                    },
                    error: function() {
                        modalBody.html('<div class="alert alert-danger">Error loading packages. Please check the console/logs.</div>');
                    }
                });
            });

            // 2. Handle Modal "Load Selected Packages" Button Click (Submission)
            $('#modal-load-selected').on('click', function() {
                let selectedPackages = $('#modal-body-content .package-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedPackages.length === 0) {
                    swal("No Packages Selected", "Please select at least one package to load.", "info");
                    return;
                }

                // Get container ID from the hidden input loaded in the modal content
                let containerId = $('#modal-body-content #modal_container_id').val();

                swal({
                    title: "Confirm Load",
                    text: "Attach selected packages to this container?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes, load them!",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.value) {
                        showModalProgressBar(containerId, selectedPackages);
                    }
                });
            });
            
            // 3. Modal Progress Bar and Final AJAX POST
            function showModalProgressBar(containerId, selectedPackages) {
                // The progress bar container is located inside the modal-body-content
                let progressBarContainer = $('#modal-body-content #modal-progress-bar-container');
                let progressBar = $('#modal-body-content #modal-progress-bar');
                let width = 0;
                
                // Hide table and footer buttons, show progress bar
                $('#modal-body-content .table-responsive').hide();
                $('#modal-load-selected, #loadPackagesModal .btn-secondary').hide();
                progressBarContainer.show();
                progressBar.css('width', '0%').text('0%');

                let interval = setInterval(() => {
                    if (width >= 100) {
                        clearInterval(interval);
                        
                        // Final AJAX POST to attach packages
                        $.ajax({
                            url: `/container/${containerId}/attachloadpackages`, // Ensure this route exists and points to your attachPackagetoContainer method
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                package_ids: selectedPackages
                            },
                            success: function(response) {
                                progressBarContainer.hide();
                                $('#loadPackagesModal').modal('hide'); 
                                if (response.success) {
                                    swal("Success", "Packages loaded successfully!", "success")
                                        .then(() => location.reload());
                                } else {
                                    swal("Error", response.message || "Failed to attach packages.", "error");
                                }
                            },
                            error: function(xhr) {
                                progressBarContainer.hide();
                                $('#loadPackagesModal').modal('hide'); 
                                swal("Error", "Server error occurred. Check server logs.", "error");
                            },
                            complete: function() {
                                // Restore visibility of table and buttons in case of failure/re-open
                                $('#modal-body-content .table-responsive').show();
                                $('#modal-load-selected, #loadPackagesModal .btn-secondary').show();
                            }
                        });

                    } else {
                        width++;
                        progressBar.css('width', width + '%').text(width + '%');
                    }
                }, 30); // Simulating 3 seconds
            }

        });
    </script>
@endpush

@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
