@extends('layouts._master')
@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">List staffs</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary"
                            href="{{ route('admin.list.staff') }}">List_staffs</a></span></p>
            </div>
            <div>
                <a href="{{ route('admin.create.staff') }}" class="btn btn-primary me-3">+ Add new</a>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="EmployeesTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        S/N
                                    </th>
                                    <th class="text-center">
                                        image
                                    </th>
                                    <th>
                                        Employee ID
                                    </th>
                                    <th>
                                        Employee Name
                                    </th>
                                    <th>
                                        Departement
                                    </th>

                                    <th>
                                        Role
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="text-center"  style="width: 10%;">
                                            @if($employee->image)
                                            <img src="{{ asset('storage/'.$employee->image) }}" alt="" width="50px" height="50px">
                                            @else 
                                            <img src="{{ asset('images/default-user.png') }}" alt="" width="50px" height="50px">
                                            @endif
                                        </td>
                                        
                                        <td style="width: 10%;">{{ $employee->staff_no ?? '-' }}</td>
                                        <td  style="width: 40%;">{{ $employee->staff_firstname . " " . $employee->staff_middlename . " " . $employee->staff_lastname  ?? '-' }}</td>
                                        <td  style="width: 20%;">{{ $employee->department->department_name ?? '-' }}</td>
                                        <td  style="width: 20%;">{{ $employee->user->user_type ?? '-' }}</td>
                                        <td>
                                            {!! user_status($employee->user->status)!!}
                                        </td>
                                        <td class="text-center" style="width: 30%;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @can('update', $employee)
                                                    <a href="{{ route('admin.edit.staff', $employee) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                @endcan
                                                @can('delete', $employee)
                                                    <form action="{{ route('admin.delete.staff', $employee) }}" method="POST" class="">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger">
                                                            <i class="fas fa-trash-alt"></i>
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


    @push('page_js')
    @endpush

@endsection
@include('layouts.partials.datatable')
