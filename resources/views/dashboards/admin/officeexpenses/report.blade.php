@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Office Expenses Report</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.expense.report') }}">
                    Expenses Report List
                </a></span>
                </p>
            </div>
            {{-- Remove the "Add Record" button if not needed for the report page --}}
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    {{-- Form for filtering expenses --}}
                    <form method="GET" action="{{ route('admin.expense.report') }}" class="mb-4">
                        <div class="d-flex flex-column flex-md-row gap-2 align-items-end">
                            {{-- Start Date Filter --}}
                            <div class="flex-grow-1 w-100">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                       value="{{ request('start_date') }}">
                            </div>

                            {{-- End Date Filter --}}
                            <div class="flex-grow-1 w-100">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                       value="{{ request('end_date') }}">
                            </div>

                            {{-- Category Filter --}}
                            <div class="flex-grow-1 w-100">
                                <label for="office_expense_categories_id">Category</label>
                                <select name="office_expense_categories_id" id="office_expense_categories_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}" {{ request('office_expense_categories_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter and Reset Buttons --}}
                            <div class="d-flex gap-2 flex-grow-1 w-100">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.expense.report') }}" class="btn btn-secondary btn-sm flex-grow-1">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                                <a href="{{ route('admin.export.report', request()->query()) }}" class="btn btn-success btn-sm flex-grow-1">
                                    <i class="fas fa-download"></i> Excel
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">Office Expenses List</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Description</th>
                                    <th>Cost</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($officeExpenses as $expense)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $expense->expenses_description }}</td>
                                        <td>{{ number_format( $expense->expenses_cost, 2) }}</td>
                                        <td>{{ $expense->category->name ?? '-' }}</td>
                                        <td>
                                            @if($expense->status == 0)
                                                Pending
                                            @elseif($expense->status == 1)
                                                Approved
                                            @else
                                                Not Approved
                                            @endif
                                        </td>
                                        <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="6">
                                            No expenses found for the selected filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total Expenses</th>
                                    <th colspan="4">{{ number_format( $totalExpenses, 2 ) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Your existing scripts can be modified to fit the new table and a new datatable for it --}}
@endpush
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')