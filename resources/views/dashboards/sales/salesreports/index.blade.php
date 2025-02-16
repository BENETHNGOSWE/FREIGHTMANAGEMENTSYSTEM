@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Salesreport Order</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Salesreport Order
                            List</a></span>
                </p>
            </div>
            <div>
                <a href="{{ route('sales.create.salesreport') }}" class="btn btn-primary me-3">+ Add CBM Record</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('sales.list.salesreport') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group pt-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('sales.list.salesreport') }}" class="btn btn-secondary">Reset</a>
                                    <a href="{{ route('sales.excel.salesreport', [
                                        'start_date' => request('start_date'),
                                        'end_date' => request('end_date'),
                                    ]) }}"
                                        class="btn btn-success">
                                        <i class="fas fa-download"></i> Download Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.sales_reports.index_title')</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        s/n
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.sales_reports.inputs.staff_id')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.sales_reports.inputs.customer_id')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.sales_reports.inputs.cbm')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.sales_reports.inputs.hub')
                                    </th>
                                    <th class="text-left">
                                        @lang('crud.sales_reports.inputs.amount2')
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesreports as $salesreport)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>{{ strtoupper($salesreport->staffs->staff_firstname) ?? '-' }}</td>
                                        <td>{{ strtoupper($salesreport->customers->customer_firstname) ?? '-' }}</td>
                                        <td>{{ strtoupper($salesreport->cbm) ?? '-' }}</td>
                                        <td>{{ strtoupper($salesreport->hub) ?? '-' }}</td>
                                        <td>{{ strtoupper($salesreport->amount2) ?? '-' }}</td>
                                        </td>
                                        <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @can('update', $salesreport)
                                                    <a href="{{ route('sales.edit.salesreport', $salesreport) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                    @endcan @can('view', $salesreport)
                                                    <a href="{{ route('sales.show.salesreport', $salesreport) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </a>
                                                    @endcan @can('delete', $salesreport)
                                                    <form class="delete_form"
                                                        action="{{ route('sales.delete.salesreport', $salesreport) }}"
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
