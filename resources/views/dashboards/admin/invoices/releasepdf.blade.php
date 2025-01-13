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
            height: 11in;
            margin: 0 auto;
            overflow: hidden;
            padding: 0.5in;
            width: 7.5in;
            background: #FFF;
            position: relative;
            /* Ensure the body has a relative position for the pseudo-element */
            border-radius: 1px;
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
        }

        /* Add a pseudo-element to display the background */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ public_path('storage/uploads/' . $client->client_logo) }}');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 40em;
            opacity: 0.05;
            z-index: -1;
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

        header address {
            float: left;
            font-size: 95%;
            font-style: normal;
            line-height: 1.25;
            margin: 0 1em 1em 0;
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

        /* Page border container */
        .page-border {
            position: absolute;
            top: 0.5in;
            right: 0.5in;
            bottom: 0.5in;
            left: 0.5in;
            border: 2px solid #b6b4b4;
            padding: 20px;
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
        table.meta {
            /* float: right; */
            width: 55em;
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


        /* Footer absolute positioning */
        footer {
            position: absolute;
            bottom: 100px;
            left: 20px;
            right: 20px;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #DDD;
            background: #FFF;
            justify-content: space-between;
        }

        footer p {
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        footer .footer-left,
        footer .footer-right {
            flex: 1;
            text-align: center;
        }

        footer .footer-left {
            text-align: left;
        }

        footer .footer-right {
            text-align: right;
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
            <h1>Release Order</h1>
        </header><br>
        <article>
            <table class="meta">
                <tr>
                    <th><span>Invoice Number</span></th>
                    <td><span>{{ $invoice->invoice_no }}</span></td>
                </tr>
                <tr>
                    <th><span>Customer Name</span></th>
                    <td><span>{{ strtoupper($invoice->customer->customer_firstname . ' ' . $invoice->customer->customer_middlename . ' ' . $invoice->customer->customer_lastname) ?? '-' }}</span></td>
                </tr>
                
                <tr>
                    <th><span>Total Amount</span></th>
                    <td><span>Tsh {{ number_format($invoice->total_amount, 2) }}</span></td>
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

        <footer>
            <span class="footer-left" style="display: inline-block; width: 48%; text-align: left;">
                <p>Released From: {{ strtoupper($client->client_name) }}</p>
            </span>

            <span class="footer-right" style="display: inline-block; width: 48%; text-align: right;">
                <p>Generated by - Kodafreight System</p>
            </span>
        </footer>

    </div>
</body>

</html>
