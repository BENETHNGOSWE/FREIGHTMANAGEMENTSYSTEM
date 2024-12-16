@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head mb-sm-4 mb-3">
            <div class="row">
                <div class="col-sm-6">
                    <div class="container-fluid">
                        <div class="row">
                            <h2 class="text-black font-w600">{{$page_heading ?? 'Subscriptions'}}</h2>
                                <p class="mb-1">Dashboard / <span>
                                    <a class="text-primary" href="{{ route('list.subscriptions') }}">Subscription</a>
                                    </span>
                                </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="basic-form col-sm-8 d-flex align-items-center justify-content-between">
                                <div class="filter-title col-5">
                                    <div class="h6">Filter by status</div>
                                </div>
                                <div class="filter-select col-7">
                                    <select id="select_subscription_status" class="form-control form-control-xl" name="subscription_status">
                                        <option value="">All</option>
                                        <option value="0">Pending</option>
                                        <option value="1">Full Paid</option>
                                        <option value="2">Partial Paid</option>
                                    </select>

                                </div>
                                
                            </div>
                            
                       
                            <div class="col-sm-4 d-flex justify-content-end">
                                <a href="{{ route('create.subscriptions') }}" class="btn btn-primary me-3">+ Add new</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="table-responsive card-table">
                    <table id="subscriptionTable" class="display dataTablesCard white-border table-responsive-xl">
                        <thead>
                            <tr>
                                <th>
                                    Client
                                </th>
                                <th class="text-left">

                                    Package
                                </th>

                                <th class="text-left">
                                    start date

                                </th>
                                <th class="text-left">
                                    Expire Date
                                </th>

                                <th class="text-left">
                                    Status
                                </th>

                                <th class="text-center">
                                    Action
                                </th>
                                <th class="text-center">
                                    Generate Token
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        {{ $subscription->client->client_name }}
                                    </td>
                                    <td>{{ $subscription->package_name ?? '-' }}</td>
                                    <td class="text-success"><b>{{ jason_date($subscription->start_date) ?? '-' }}</b></td>

                                    <td class="text-danger"><b>{{ jason_date($subscription->expire_date) ?? '-' }}</b>
                                        @php
                                            $today = date('Y-m-d');
                                            $expire_date = $subscription->expire_date;
                                            $diff = date_diff(date_create($today), date_create($expire_date));
                                            $days = $diff->format('%a');
                                            // The line $days = $diff->format("%a"); is using PHP’s DateTime::format method. This method returns a formatted string representation of the date.
                                            // "%a" is the format parameter, which represents the total number of days as a result of a date_diff operation. So, $diff->format("%a") will return the total number of days between the current date and the subscription’s expiration date.
                                        @endphp
                                        @if ($days <= 30)
                                            <span class="badge badge-rounded badge-danger"> {{ $days }} Days</span>
                                        @else
                                            <span class="badge badge-rounded badge-success"> {{ $days }} Days</span>
                                        @endif



                                    </td>
                                    <td>{!! return_status_badge($subscription->status) !!}</td>

                                    <td class="text-center" style="width: 134px;">
                                        <div role="group" aria-label="Row Actions" class="btn-group">
                                            @can('update', $subscription)
                                                <a href="{{ route('edit.subscriptions', $subscription) }}">
                                                    <button type="button" class="btn btn-outline-success">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </a>
                                                @endcan
                                                @can('view', $subscription)
                                                <a href="#"  class="show_subscription" data-id="{{ $subscription->id }}">
                                                    <button type="button" class="btn btn-outline-primary mx-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </a>
                                            @endcan

                                            @can('delete', $subscription)
                                                <form class="delete_form"
                                                    action="{{ route('delete.subscriptions', $subscription) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger delete_button">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                            


                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form class="delete_form"
                                                    action="{{ route('generate.subscriptions_tokens_',$subscription) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                                                    <button type="submit" class="btn btn-rounded btn-info">
                                                        <span class="text-light mx-1"><i class="fa-solid fa-arrows-rotate"></i></span>generate
                                                      </button>
                                                </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center py-5" colspan="15">
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
            var table = $("#subscriptionTable").DataTable({
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


<script>
    $(function() {
        $('#select_subscription_status').on('change', function() {
            var subscription_status = $(this).val();
            if (subscription_status == '') {
                window.location = "{{ route('list.subscriptions')}}"
            }else {
                window.location = "{{ route('subscriptions.status')}}?type_id=" + subscription_status;
            }
        });
    })
</script>




@endpush 
@include('layouts.partials.datatable') 





