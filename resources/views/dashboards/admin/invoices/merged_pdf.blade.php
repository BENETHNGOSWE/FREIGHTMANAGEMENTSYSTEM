<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merged Invoices</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Apply Poppins font globally */
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        table,
        th,
        td,
        address {
            font-family: 'Poppins', sans-serif;
        }

        /* heading */
        h1 {
            font: bold 100% 'Poppins', sans-serif;
            letter-spacing: 0.5em;
            text-align: center;
            text-transform: uppercase;
        }

        /* table */
        table {
            font-size: 75%;
            table-layout: fixed;
            width: 100%;
            border-collapse: separate;
            border-spacing: 2px;
        }

        th,
        td {
            border-width: 1px;
            padding: 0.5em;
            position: relative;
            text-align: left;
            border-radius: 0.25em;
            border-style: solid;
        }

        th {
            background: #EEE;
            border-color: #BBB;
        }

        td {
            border-color: #DDD;
        }

        body {
            box-sizing: border-box;
            margin: 0 auto;
            padding: 0.5in;
            width: 7.5in;
            background: #FFF;
            position: relative;
            border-radius: 1px;
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
        }

        /* Watermark */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ public_path('storage/uploads/' . $company->logo) }}');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 40em;
            opacity: 0.05;
            z-index: -1;
        }

        /* header */
        header {
            margin: 0 0 1em;
            page-break-inside: avoid;

        }

        header:after {
            clear: both;
            content: "";
            display: table;
        }

        header h1 {
            background: #0118e4;
            border-radius: 0.25em;
            color: #FFF;
            margin: 0 0 1em;
            padding: 0.5em 0;
        }

        header address {
            float: left;
            font-size: 95%;
            font-style: normal;
            line-height: 1.25;
            margin: 0 1em 1em 0;
        }

        .page-border {
            position: absolute;
            top: 0.5in;
            right: 0.5in;
            bottom: 0.5in;
            left: 0.5in;
            border: 2px solid #b6b4b4;
            padding: 20px;
        }

        article address.norm h4 {
            font-size: 125%;
            font-weight: bold;
        }

        article address.norm {
            float: left;
            font-size: 95%;
            font-style: normal;
            font-weight: normal;
            line-height: 1.25;
            margin: 0 1em 1em 0;
        }

        header address p {
            margin: 0 0 0.25em;
        }

        header span,
        header img {
            display: block;
            float: right;
        }

        header span {
            margin: 0 0 1em 1em;
            max-height: 25%;
            max-width: 60%;
            position: relative;
        }

        header img {
            max-height: 100%;
            max-width: 100%;
        }

        /* article */
        article,
        article address,
        table.meta,
        table.inventory {
            margin: 0 0 3em;
        }

        article:after {
            clear: both;
            content: "";
            display: table;
        }

        article h1 {
            clip: rect(0 0 0 0);
            position: absolute;
        }

        article address {
            float: left;
            font-size: 125%;
            font-weight: bold;
        }

        /* table meta & balance */
        table.meta,
        table.balance {
            float: right;
            width: 36%;
        }

        table.meta:after,
        table.balance:after {
            clear: both;
            content: "";
            display: table;
        }

        /* table meta */
        table.meta th {
            width: 40%;
        }

        table.meta td {
            width: 60%;
        }

        /* table items */
        table.inventory {
            clear: both;
            width: 100%;
            /* NEW: Allow table to break across pages */
            page-break-inside: auto;
        }

        /* NEW: Ensure table body allows internal breaking */
        table.inventory tbody {
            page-break-inside: auto;
        }
        
        /* NEW: CRITICAL RULE - Prevents a single row from splitting, but allows the break to happen between rows */
        table.inventory tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.inventory th:first-child {
            width: 50px;
        }

        table.inventory th {
            font-weight: bold;
            text-align: center;
        }

        table.inventory td {
            text-align: center;
        }

        table.inventory td:nth-child(1) {
            width: 5%;
        }


        /* table balance */
        table.balance th,
        table.balance td {
            width: 50%;
            text-align: right;
        }

        .company-header {
            text-align: left;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .company-logo {
            flex: 0 0 150px;
            /* Fixed width for logo container */
            margin-top: 10px;
        }

        .company-logo img {
            max-width: 100%;
            height: auto;
        }

        .company-info {
            flex: 1;
            text-align: left;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 14px;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        /* aside */
        aside h1 {
            border: none;
            border-width: 0 0 1px;
            margin: 0 0 1em;
            border-color: #999999;
            border-bottom-style: solid;
        }

        table.sign {
            float: left;
            width: 220px;
        }

        table.sign img {
            width: 100%;
        }

        table.sign tr td {
            border-color: transparent;
        }

        .payment-info {
            margin-top: 2em;
            /* Adds space above the payment info */
        }

        .payment-info h2 {
            font-size: 1.5em;
            /* Adjusts the font size for the header */
            margin-bottom: 1em;
            /* Space below the header */
        }

        .payment-info p {
            margin: 0.2em 0;
            font-size: 15px;
            /* Adds space between each paragraph */
        }

        /* Footer absolute positioning */
        footer {
            position: absolute;
            bottom: 100px;
            left: 20px;
            right: 20px;
            padding: 10px 0;
            border-top: -7em solid #DDD;
            justify-content: space-between;
            page-break-inside: avoid;
            page-break-before: always;
        }

        footer .footer-left {
            text-align: left;
        }

        @media print {

            * {
                -webkit-print-color-adjust: exact;
            }

            html {
                background: none;
                padding: 0;
            }

            body {
                box-shadow: none;
                margin: 0;
            }

            span:empty {
                display: none;
            }

            .add,
            .cut {
                display: none;
            }

            .page-border {
                border: 2px solid #000 !important;
            }
        }

        @page {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="page-border">
        <header>
            <h1>CONSOLIDATED INVOICE</h1>
            <div class="company-header">
                <div class="company-logo">
                    @if ($company->logo)
                        <img alt="logko" src="{{ public_path('storage/uploads/' . $company->logo) }}"
                            style="width: 150px; height: auto;">
                    @endif
                </div>
                <div class="company-info">
                    <div class="company-name">{{ strtoupper($company->company_name) }}</div>
                    <div class="company-details">
                        Office Phone: {{ strtoupper($company->office_phone) }}<br>
                        TIN Number: {{ strtoupper($company->tin_number) }}<br>
                        Office Add: {{ strtoupper($company->tagline) }}
                    </div>
                </div>
            </div>
        </header>
        <hr>
        <article>
            <p>To: </p>
            <address class="norm" style="margin-top: -1em">
                <h4 style="margin: 0; padding: 0; color:#0118e4;">{{ $customer->customer_firstname }}
                    {{ $customer->customer_lastname }}</h4>
                <p style="margin: 0; padding: 0;">{{ $customer->customer_email }}</p>
                <p style="margin: 0; padding: 0;">{{ $customer->customer_address }}</p>
                <p style="margin: 0; padding: 0;">{{ $customer->phone }}</p>
                <p style="margin: 0; padding: 0;">Phone: {{ $customer->phone }}</p>
            </address>


            <table class="meta">
                <tr>
                    <th><span>Invoice #s</span></th>
                    <td><span>{{ $totalInvoiceNo }}</span></td>
                </tr>
                <tr>
                    <th><span>Date</span></th>
                    <td><span>{{ \Carbon\Carbon::now()->format('F j, Y') }}</span></td>
                </tr>
                <tr>
                    <th><span>Total Amount Due</span></th>
                    <td><span>Tsh
                        </span><span>{{ number_format($totalAmountDue, 2) }}</span>
                    </td>
                </tr>
            </table>

            <table class="inventory">
                <thead>
                    <tr>
                        <th><span>S. No</span></th>
                        <th><span>Invoice No</span></th> 
                        <th><span>Load Name</span></th>
                        <th><span>Description</span></th>
                        <th><span>Load Unity</span></th>
                        <th><span>Price ($)</span></th>
                        <th><span>Total USD ($)</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allItems as $invoiceLoad)
                        <tr>
                            <td><span>{{ $loop->iteration }}</span></td>
                            <td><span>{{ $invoiceLoad->invoice->invoice_no ?? 'N/A' }}</span></td>
                            <td><span>{{ $invoiceLoad->loadpackage->load_name }}</span></td>
                            <td><span>{{ $invoiceLoad->loadpackage->load_description }}</span></td>
                            <td><span>{{ $invoiceLoad->loadpackage->load_weight_value }}</span></td>
                            <td><span>{{ number_format($invoiceLoad->invoice->cbm_price ?? 0, 2) }}</span></td>
                            <td><span>{{ number_format(($invoiceLoad->loadpackage->load_weight_value ?? 0) * ($invoiceLoad->invoice->cbm_price ?? 0), 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="payment-info" style="font-size: 12px; margin-top: -1em;">
                <h2 style="margin: 0; padding: 0;">Payment Info</h2>
                @forelse ($paymentInfo as $payment)
                    <p><strong>Bank Name:</strong> {{ $payment->Bank_name }}</p>
                    <p><strong>Acc Name:</strong> {{ $payment->account_name }}</p>
                    <p><strong>Acc No:</strong> {{ $payment->account_number }}</p><br>
                @empty
                    <p>No payment information available.</p>
                @endforelse
            </div>
            <table class="balance" style="margin-top: -1em">
                <tr>
                    <th><span>Exchange Rate ($)</span></th>
                    <td><span>{{ number_format($exchangeRate, 2) }}</span></td>
                </tr>
                <tr>
                    <th><span>TOTAL (USD)</span></th>
                    <td><span>{{ number_format($totalAmount, 2) }}</span></td>
                </tr>
            </table>
        </article>
        <header>
            <span style="margin-top: -1em; font-size: 10px">
                <p style="margin: 0; font-weight: bold;">Approved By:</p>
                <p style="margin: 5px 0;">Signature: __________________________</p>
                <p style="margin: 5px 0;">Name: __________________________</p>
                <p style="margin: 5px 0;">Date: __________________________</p>
            </span>
            <address style="margin-top: -1em; font-size: 10px">
                <p style="margin: 0; font-weight: bold;">Printed By:</p>
                <p style="margin: 5px 0;">Name: <strong>{{ auth()->user()->name ?? 'System User' }}</strong></p>
                <p style="margin: 5px 0;">Date:
                    <strong>{{ \Carbon\Carbon::now('Africa/Nairobi')->format('F j, Y, g:i A') }}</strong>
                </p>
            </address>
        </header>

        <footer>
            <aside class="footer-left" style="display: inline-block; text-align: left; font-size: 12px; margin-top:-3em">
                <h4 style="margin-top: 3em">Terms & Conditions</h4>
                <div>
                    <p style="margin: 0; padding: 0;"><strong>Delivery Timeline:</strong> Products will be delivered within
                        7 days of receiving the Local Purchase Order.</p>
                    <p style="margin: 0; padding: 0;"><strong>Payment Terms:</strong> Payment is due 30 days after the
                        receipt of the products.</p>
                    <p style="margin: 0; padding: 0;"><strong>Delivery Location:</strong> Products will be delivered to the
                        address specified in the LPO.</p>
                </div>
            </aside>
            <hr>
            <aside class="footer-left" style="font-size: 10px">
                <div>
                    <p style="margin: 0; padding: 0;">
                        {{ $company->company_address_one }} P.O. Box
                        {{ $company->tin_number }} 
                        {{ $company->company_city }}, {{ $company->company_country }} | Tel:
                        {{ $company->office_phone }} Email: {{ $company->company_email }}
                    </p>

                </div>
            </aside>
        </footer>
    </div>
</body>

</html>
