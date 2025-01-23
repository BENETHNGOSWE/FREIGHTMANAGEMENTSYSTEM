<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Release Order</title>
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
        span {
            font-family: 'Poppins', sans-serif;
        }

        /* heading */
        h1 {
            font: bold 100% 'Poppins', sans-serif;
            letter-spacing: 0.5em;
            text-align: center;
            text-transform: uppercase;
        }

        body {
            box-sizing: border-box;
            height: 11in;
            margin: 0 auto;
            padding: 0.5in;
            width: 7.5in;
            background: #FFF;
            border-radius: 1px;
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            position: relative;
        }

        header {
            margin: 0 0 1em;
        }

        header address {
            float: left;
            font-size: 95%;
            font-style: normal;
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



        /* Page border container */
        .page-border {
            position: absolute;
            top: 0.5in;
            right: 0.5in;
            bottom: 0.5in;
            left: 0.5in;
            border: 2px solid #000;
            padding: 20px;
        }

        /* Inner content wrapper for padding from border */
        .inner-content {
            position: relative;
            height: 100%;
            padding: 15px;
        }

        /* header */
        header {
            margin: 0 0 1em;
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

        /* Content styling */
        .release-content {
            margin: 2em 0;
            padding: 1em;
            border: 1px solid #DDD;
            border-radius: 0.25em;
        }

        .release-content p {
            margin: 0.5em 0;
            padding: 0.5em;
            border-bottom: 1px solid #EEE;
            font-size: 95%;
        }

        .release-content p strong {
            color: #0118e4;
            width: 150px;
            display: inline-block;
        }

        /* Signature section */
        .signature-section {
            margin-top: 3em;
            border-top: 1px solid #DDD;
            padding-top: 1em;
        }

        .signature-box {
            margin: 1em 0;
        }

        .signature-box p {
            margin: 0.3em 0;
            font-size: 90%;
        }

        .status-paid {
            color: #28a745;
            font-weight: bold;
        }

        .status-unpaid {
            color: #dc3545;
            font-weight: bold;
        }

        /* Table Styling */
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        .table-bordered th,
        .table-bordered td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table-bordered th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #333;
        }

        .table-bordered td {
            background-color: #fff;
        }

        .table-header {
            background-color: #007bff;
            color: white;
            padding: 8px;
        }

        /* Status Colors */
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }

        .status-unpaid {
            color: #dc3545;
            font-weight: bold;
        }

        /* Styling for Table Borders */
        .table-bordered {
            border: 1px solid #ddd;
            border-radius: 0.25rem;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
        }


        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #ddd;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }

        /* Footer absolute positioning */
        footer {
            position: absolute;
            bottom: 60px;
            /* Adjusted to account for border padding */
            left: 20px;
            right: 20px;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #DDD;
            background: #FFF;
            display: flex;
            justify-content: space-between;
        }

        footer p {
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        footer .footer-left,
        footer .footer-right {
            flex: 1;
            /* Each div takes equal space */
            text-align: center;
        }

        footer .footer-left {
            text-align: left;
            /* Align left */
        }

        footer .footer-right {
            text-align: right;
            /* Align right */
        }

        /* Main content wrapper - Add padding bottom to prevent content overlapping footer */
        .main-content {
            padding-bottom: 100px;
            /* Space for footer */
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
        <div class="inner-content">
            <div class="main-content">
                <header>
                    <h1>Release Order</h1>
                </header>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="table-header">Invoice No:</th>
                            <td style="padding-left: 20px;">{{ $invoice->invoice_no }}</td>
                            <!-- Added left padding for space -->
                        </tr>
                        <tr>
                            <th class="table-header">Customer:</th>
                            <td>{{ $invoice->customer->customer_firstname }}</td>
                        </tr>
                        <tr>
                            <th class="table-header">Total Amount:</th>
                            <td>Tsh {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="table-header">Status:</th>
                            <td>
                                <span class="{{ $invoice->status == 1 ? 'status-paid' : 'status-unpaid' }}">
                                    {{ $invoice->status == 1 ? 'Full Paid' : 'Not Paid' }}
                                </span>
                            </td>
                        </tr>
                    </thead>
                </table>



                <header>
                    <br>
                    <address style="margin-top: -1em; font-size: 10px">
                        <p style="margin: 0; font-weight: bold;">Release By:</p>
                        <p style="margin: 5px 0;">Name: <strong>{{ auth()->user()->name }}</strong></p>
                        <p style="margin: 5px 0;">Date:
                            <strong>{{ \Carbon\Carbon::now('Africa/Nairobi')->format('F j, Y, g:i A') }}</strong>
                        </p>
                    </address>
                    <span style="margin-top: -1em; font-size: 10px">
                        <p style="margin: 0; font-weight: bold;">Approved By:</p>
                        <p style="margin: 5px 0;">Signature: __________________________</p>
                        <p style="margin: 5px 0;">Name: __________________________</p>
                        <p style="margin: 5px 0;">Date: __________________________</p>
                    </span>
                </header>
            </div>

            <footer>
                <div class="footer-left">
                    <p>Release From: {{ strtoupper($client->client_name) }}</p>
                </div>
                <div class="footer-right">
                    <p>Generated by Kodafreight System</p>
                </div>
            </footer>

        </div>
    </div>
</body>

</html>






<article>
    <table class="meta">
        <tr>
            <th><span>Invoice Number</span></th>
            <td><span>{{ $invoice->invoice_no }}</span></td>
        </tr>
        <tr>
            <th><span>Customer Name</span></th>
            <td><span>{{ strtoupper($invoice->customer->customer_firstname . ' ' . $invoice->customer->customer_middlename . ' ' . $invoice->customer->customer_lastname) ?? '-' }}</span>
            </td>
        </tr>

        <tr>
            <th><span>Customer Number</span></th>
            <td><span>{{ strtoupper($invoice->customer->phone) }}</span></td>
        </tr>

        <tr>
            <th><span>Status</span></th>
            <td><span class="{{ $invoice->status == 1 ? 'status-paid' : 'status-unpaid' }}">
                    {{ $invoice->status == 1 ? 'Full Paid' : 'Not Paid' }}
                </span></td>
        </tr>
    </table>
</article>
<header>
    <address style="margin-top: -1em; font-size: 10px">
        <p style="margin: 0; font-weight: bold;">Released By:</p>
        <p style="margin: 5px 0;">Name: <strong>{{ auth()->user()->name }}</strong></p>
        <p style="margin: 5px 0;">Date:
            <strong>{{ \Carbon\Carbon::now('Africa/Nairobi')->format('F j, Y, g:i A') }}</strong>
        </p>
    </address>
    <span style="margin-top: -1em; font-size: 10px">
        <p style="margin: 0; font-weight: bold;">Approved By:</p>
        <p style="margin: 5px 0;">Signature: __________________________</p>
        <p style="margin: 5px 0;">Name: __________________________</p>
        <p style="margin: 5px 0;">Date: __________________________</p>
    </span>
</header>
