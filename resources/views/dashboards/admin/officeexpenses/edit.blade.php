@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Expense Cost</h2>
            
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('admin.list.officeexpense') }}">Edit Expense Cost</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('admin.update.officeexpense', $officeexpense ) }}"
                class="mt-4"
            >

            <div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label for="expenses_description">Expense Description</label>
            <input
                type="text"
                name="expenses_description"
                id="expenses_description"
                class="form-control"
                value="{{ old('expenses_description', $officeexpense->expenses_description) }}"
                maxlength="255"
                placeholder="Expense Description"
                required
            />
            @error('expenses_description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="expenses_cost">Expenses Amount Cost</label>
            <input
                type="text"
                name="expenses_cost"
                id="expenses_cost"
                class="form-control"
                value="{{ old('expenses_cost', $officeexpense->expenses_cost) }}"
                maxlength="255"
                placeholder="Expenses Amount Cost"
            />
            @error('expenses_cost')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="office_expense_categories_id">Expenses Category</label>
            <select
                name="office_expense_categories_id"
                id="office_expense_categories_id"
                class="form-control"
            >
                <option disabled {{ empty(old('office_expense_categories_id', $officeexpense->office_expense_categories_id)) ? 'selected' : '' }}>
                    Please select the Category
                </option>
                @foreach ($categories_expenses as $value => $label)
                    <option value="{{ $value }}" {{ old('office_expense_categories_id', $officeexpense->office_expense_categories_id) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('office_expense_categories_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    @if($canUpdateExpensesStatus)
        <div class="col-md-6">
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="0" {{ old('status', $officeexpense->status) == '0' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="1" {{ old('status', $officeexpense->status) == '1' ? 'selected' : '' }}>
                        Approved
                    </option>
                    <option value="2" {{ old('status', $officeexpense->status) == '2' ? 'selected' : '' }}>
                        Not Approved
                    </option>
                </select>
                @error('status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            
        </div>
    @endif
</div>
            
           

                <div class="mt-4">
                    <a
                        href="{{ route('admin.list.officeexpense') }}"
                        class="btn btn-light"
                    >
                        <i class="fa-solid fa-backward"></i>
                        @lang('crud.common.back')
                    </a>

                    <a
                        href="{{ route('admin.create.officeexpense') }}"
                        class="btn btn-light"
                    >
                        <i class="icon ion-md-add text-primary"></i>
                        @lang('crud.common.create')
                    </a>

                    <button type="submit" class="btn btn-primary float-right">
                        <i class="icon ion-md-save"></i>
                        @lang('crud.common.update')
                    </button>
                </div>
            </x-form>
        </div>
    </div>
</div>
@endsection
