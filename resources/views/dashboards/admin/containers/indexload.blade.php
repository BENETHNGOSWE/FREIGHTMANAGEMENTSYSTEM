@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Load Packages to Container</h2>
            <p class="mb-1">Dashboard / <span class="text-primary">Load Packages</span></p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- Progress Bar -->
            <div id="progress-bar-container" style="display:none; width:100%; background-color:#ddd; margin-bottom:15px;">
                <div id="progress-bar" style="width:0%; height:25px; background-color:#4caf50; text-align:center; color:white;">
                    0%
                </div>
            </div>

            <div class="table-responsive">
                <table id="loadTable" class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th style="width:50px;">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>Customer Name</th>
                            <th>Load No</th>
                            <th>Load Name</th>
                            <th>Weight</th>
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allloads as $loadpackage)
                            <tr>
                                <td>
                                    <input 
                                        type="checkbox" 
                                        class="package-checkbox" 
                                        value="{{ $loadpackage->id }}"
                                        {{ in_array($loadpackage->id, $loadedIdsForContainer) ? 'checked' : '' }}>
                                </td>
                                <td>{{ strtoupper($loadpackage->customer->customer_firstname . ' ' . $loadpackage->customer->customer_lastname) ?? '-' }}</td>
                                <td style="color:green; font-weight:700;">{{ strtoupper($loadpackage->load_no) ?? '-' }}</td>
                                <td>{{ strtoupper($loadpackage->load_name) ?? '-' }}</td>
                                <td>{{ strtoupper($loadpackage->load_weight_value . ' ' . $loadpackage->unittype->unittype_name) ?? '-' }}</td>
                                <td style="color:rgb(255,117,19); font-weight:700;">{{ strtoupper($loadpackage->load_volume) ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">No packages found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="mt-3 text-end">
                <button id="load-selected" class="btn btn-primary">
                    <i class="fas fa-truck-loading"></i> Load Selected Packages
                </button>
            </div>

            <input type="hidden" id="container_id" value="{{ $container_id }}">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Select/Deselect all checkboxes
    $('#select-all').on('click', function() {
        $('.package-checkbox').prop('checked', this.checked);
    });

    // Handle Load Selected Packages
    $('#load-selected').on('click', function() {
        let selectedPackages = $('.package-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedPackages.length === 0) {
            swal("No Packages Selected", "Please select at least one package to load.", "info");
            return;
        }

        let containerId = $('#container_id').val();

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
                showProgressBar(containerId, selectedPackages);
            }
        });
    });
});

function showProgressBar(containerId, selectedPackages) {
    let progressBarContainer = $('#progress-bar-container');
    let progressBar = $('#progress-bar');
    let width = 0;
    progressBarContainer.show();

    let interval = setInterval(() => {
        if (width >= 100) {
            clearInterval(interval);

            // AJAX POST
            $.ajax({
                url: `/container/${containerId}/attachloadpackages`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    package_ids: selectedPackages
                },
                success: function(response) {
                    progressBarContainer.hide();
                    if (response.success) {
                        swal("Success", "Packages loaded successfully!", "success")
                            .then(() => location.reload());
                    } else {
                        swal("Error", response.message || "Failed to attach packages.", "error");
                    }
                },
                error: function() {
                    progressBarContainer.hide();
                    swal("Error", "Server error occurred.", "error");
                }
            });

        } else {
            width++;
            progressBar.css('width', width + '%').text(width + '%');
        }
    }, 30);
}
</script>
@endpush

@include('layouts.partials.sweetalert')
