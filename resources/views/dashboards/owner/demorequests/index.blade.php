@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Demo Request</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Demo request List</a></span></p>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">Demo request List</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>s/n</th>
                                    <th class="text-left">Client Name</th>
                                    <th class="text-left">Email Address</th>
                                    <th class="text-left">Phone Number</th>
                                    <th class="text-left">Message</th>
                                    <th class="text-left">Company Name</th>
                                    <th class="text-left">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demorequests as $demoRequest)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper($demoRequest->full_name) ?? '-' }}</td>
                                        <td>{{ $demoRequest->email_address ?? '-' }}</td>
                                        <td>{{ $demoRequest->phone_number ?? '-' }}</td>
                                        <td class="wrap-text" title="{{ $demoRequest->message ?? '-' }}">
                                            @php
                                                $message = strtoupper($demoRequest->message ?? '-');
                                                $words = str_split($message, 50);
                                                echo implode('<br>', $words);
                                            @endphp
                                        </td>
                                        <td>{{ strtoupper($demoRequest->company_name) ?? '-' }}</td>
                                        <td>{{ strtoupper($demoRequest->city) ?? '-' }}</td>
                                      
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="8">
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
@endsection

@push('scripts')
    <script>
        (function($) {
            var table = $("#loadtypeTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
                autoWidth: false,
                columnDefs: [
                    { targets: 6, width: '250px' }
                ]
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
    </script>
@endpush

@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')

<style>
    .wrap-text {
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        max-width: 250px;
        word-break: break-all; 
    }
</style>