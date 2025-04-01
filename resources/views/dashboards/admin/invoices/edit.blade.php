@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Invoice Status</h2>
        </div>
        <div>
            <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.list.invoice') }}">Edit Invoice</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <x-form method="PUT" action="{{ route('admin.update.invoice', $invoice) }}" class="mt-4">
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

                    <!-- Display load_weight_value for each related load -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Load Weight Values</label>
                            @if($invoice->invoiceLoads->isNotEmpty())
                                <ul class="list-group">
                                    @foreach($invoice->invoiceLoads as $invoiceLoad)
                                        <li class="list-group-item">
                                            
                                            {{ number_format($invoiceLoad->loadPackage->load_weight_value ?? 0, 10) }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No loads associated with this invoice.</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cbm_price">CBM Price</label>
                            <input type="number" step="0.01" name="cbm_price" value="{{ old('cbm_price', $invoice->cbm_price ?? '') }}" class="form-control" placeholder="Enter CBM Price">
                            @error('cbm_price') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exchange_rate">Exchange Rate</label>
                            <input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', $invoice->exchange_rate ?? '') }}" class="form-control" placeholder="Enter Exchange Rate">
                            @error('exchange_rate') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                   
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.list.invoice') }}" class="btn btn-light">
                        <i class="fa-solid fa-backward"></i> @lang('crud.common.back')
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