@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Active Name</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Name List</a></span>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.create.activename') }}" class="btn btn-primary me-3">+ Add Name</a>
            </div>
        </div>

        {{-- NOTIFICATION TOGGLE CARD - ADD THIS --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="card bg-light border-primary">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <i class="fas fa-bell me-2"></i>Customer SMS Notifications
                                </h5>
                                <small class="text-muted">
                                    <span id="notification-status-text">
                                        @if(session('send_notifications', true))
                                            Notifications are <strong class="text-success">ENABLED</strong> - Customers will receive SMS when loads are created
                                        @else
                                            Notifications are <strong class="text-danger">DISABLED</strong> - No SMS will be sent to customers
                                        @endif
                                    </span>
                                </small>
                            </div>
                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="notification_toggle" 
                                    {{ session('send_notifications', true) ? 'checked' : '' }}
                                    style="width: 3.5rem; height: 2rem; cursor: pointer;"
                                >
                                <label class="form-check-label ms-2" for="notification_toggle">
                                    <strong id="toggle-label">
                                        {{ session('send_notifications', true) ? 'ON' : 'OFF' }}
                                    </strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">Name List</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>s/n</th>    
                                    <th class="text-left">Client Name</th>
                                    <th class="text-left">Sender Name</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientActivenames as $activename)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $activename->client->client_name ?? '-' }}</td>
                                        <td>{{$activename->sendername->name}}</td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                <a href="{{ route('admin.edit.activename', $activename) }}">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="4">
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
            });

            // Notification Toggle Handler
            $('#notification_toggle').on('change', function() {
                const isEnabled = $(this).is(':checked');
                const toggle = $(this);
                
                $.ajax({
                    url: '{{ route("admin.toggle.notification") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        enabled: isEnabled
                    },
                    beforeSend: function() {
                        toggle.prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI
                            $('#toggle-label').text(isEnabled ? 'ON' : 'OFF');
                            
                            if (isEnabled) {
                                $('#notification-status-text').html(
                                    'Notifications are <strong class="text-success">ENABLED</strong> - Customers will receive SMS when loads are created'
                                );
                                toastr.success('SMS notifications enabled for all new loads');
                            } else {
                                $('#notification-status-text').html(
                                    'Notifications are <strong class="text-danger">DISABLED</strong> - No SMS will be sent to customers'
                                );
                                toastr.warning('SMS notifications disabled. Customers will NOT receive messages');
                            }
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update notification settings');
                        toggle.prop('checked', !isEnabled); // Revert
                    },
                    complete: function() {
                        toggle.prop('disabled', false);
                    }
                });
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