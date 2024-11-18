@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Invoice</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="">Invoice
                            Show</a></span></p>
            </div>

        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">


                    <div class="row border-bottom mb-2">
                        <div class="col-md-4">
                            <h4>Invoice</h4>
                        </div>

                        <div class="col-md-4">
                            <h4>04/10/2024</h4>
                        </div>

                        <div class="col-md-4">
                            <h4>status: <span>{!! invoice_status($invoice->status) !!}</span></h4>
                            <a href="{{ route('admin.pdf.invoice', $invoice->id) }}" class="btn btn-primary mt-2">Download PDF</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <p>From:</p>
                            <div>
                                <strong>
                                    <strong>{{ $client->client_name }}</strong>
                                </strong>
                            </div>

                            <div>
                                <div>{{ $client->client_address_one }}</div>
                            </div>

                            <div>
                                <div>{{ $client->client_location }}</div>
                            </div>

                            <div>
                                <div>{{ $client->client_email }}</div>
                            </div>

                            <div>
                                <div>{{ $client->client_phonenumber }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <p>To:</p>
                            <div>
                                <strong>
                                    {{ $invoice->customer->customer_firstname }}
                                </strong>
                            </div>

                            <div>
                                location: <span>{{ $invoice->customer->customer_address }}</span>
                            </div>

                            <div>
                                Email: <span>{{ $invoice->customer->customer_email }}</span>
                            </div>

                            <div>
                                Phonenumber: <span>{{ $invoice->customer->phone }}</span>
                            </div>

                        </div>

                        {{-- <div class="col-md-4">
                            <img style="max-width: 100px" src="{{ asset('images/logo.png') }}" class="width110"
                                alt="" />
                        </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table  table-striped mt-5 table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-start" scope="row">Invoice No</th>
                                            <th class="text-start" scope="row">Description</th>
                                            <th class="text-start" scope="row">Load Price</th>
                                            <th class="text-start" scope="row">Transport Fee</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->invoiceLoads as $invoiceLoad)
                                            <tr>
                                                <td>{{ $invoiceLoad->invoice->invoice_no }}</td>
                                                <td>{{ $invoiceLoad->loadpackage->load_description }}</td>
                                                <td>{{ $invoiceLoad->loadpackage->load_price }}</td>
                                                <td>{{ $invoiceLoad->loadpackage->load_transportfee }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-5"></div>
                        <div class="col-lg-4 col-sm-5 ms-auto">
                            <table class="table table-clear">
                                <tbody>
                                    <tr>
                                        <td class="left"><strong>Transport Fee </strong></td>
                                        <td class="right">
                                            <strong>{{ $invoice->total_transportfee }}</strong><br />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="left"><strong>Balance </strong></td>
                                        <td class="right">
                                            <strong>{{ $invoice->total_amount }}</strong><br />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="left"><strong>Total </strong></td>
                                        <td class="right">
                                            <strong>{{ number_format($invoice->total_amount + $invoice->total_transportfee, 2) }} /=</strong><br />

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if (empty($invoice->remark))
                        no remarks;
                    @else
                        <div class="">
                            <h4 class="text-black">Remark</h4>
                            <p class="text-black">{{ $invoice->remark }}</p>
                        </div>
                    @endif




                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        S/N
                                    </th>
                                    <th class="text-left">
                                        Prefix
                                    </th>

                                    <th class="text-left">
                                        Payment Type
                                    </th>
                                    <th class="text-left">
                                        Amount
                                    </th>

                                    <th class="text-left">
                                        Date Paid
                                    </th>

                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td style="width: 30px !important;"></td>
                                    <td></td>

                                </tr>

                                <tr>
                                    <td class="text-center py-5" colspan="5">
                                        @lang('crud.common.no_items_found')
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
    @push('scripts')
        <script>
            (function($) {
                var table = $("#hospitalsTable").DataTable({
                    searching: true,
                    paging: true,
                    select: false,
                    lengthChange: false,
                });
            })(jQuery);
        </script>
    @endpush
@endsection
