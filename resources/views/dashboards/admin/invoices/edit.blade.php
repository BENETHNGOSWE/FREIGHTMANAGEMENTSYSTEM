@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Invoice Status</h2>
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="{{ route('admin.list.invoice') }}">Edit Invoice</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('admin.update.invoice', $invoice) }}"
                class="mt-4"
            >
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invoice_no">Invoice Number</label>
                        <input type="text" value="{{ $invoice->invoice_no }}" name="invoice_no" class="form-control" placeholder="Invoice Number" readonly>
                        @error('invoice_no') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Invoice Status</label>
                        <select name="status" class="form-control">
                            <option value="0" {{ $invoice->status == 0 ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ $invoice->status == 2 ? 'selected' : '' }}>Partial Paid</option>
                            <option value="1" {{ $invoice->status == 1 ? 'selected' : '' }}>Full Paid</option>
                        </select>
                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                
               
            </div>
            
            <div class="mt-4">
                <a href="{{ route('admin.list.invoice') }}" class="btn btn-light">
                    <i class="fa-solid fa-backload"></i> @lang('crud.common.back')
                </a>

                <button type="submit" class="btn btn-primary float-right">
                    <i class="icon ion-md-save"></i> @lang('crud.common.update')
                </button>
            </div>

            </x-form>
        </div>
    </div>
</div>
@endsection
