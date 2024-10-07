<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
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
            border-radius: 1px;
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
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
        }

        table.inventory th:first-child {
            width: 50px;
        }

        table.inventory th:nth-child(2) {
            width: 300px;
        }

        table.inventory th {
            font-weight: bold;
            text-align: center;
        }

        table.inventory td:nth-child(1) {
            width: 26%;
        }

        table.inventory td:nth-child(2) {
            width: 38%;
        }

        table.inventory td:nth-child(3),
        table.inventory td:nth-child(4),
        table.inventory td:nth-child(5) {
            text-align: right;
            width: 12%;
        }

        /* table balance */
        table.balance th,
        table.balance td {
            width: 50%;
            text-align: right;
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
        }

        @page {
            margin: 0;
        }
    </style>
</head>

<body>
    <header>
        <h1>Invoice</h1>
        <p>From: </p>
        <span style="margin-top: -1em">
            @if ($client->client_logo)
                <img alt="logo" src="{{ public_path('storage/uploads/' . $client->client_logo) }}"
                    style="width: 150px;">
            @endif
        </span>
        <address style="margin-top: -1em">
            <p style="color:#0118e4;"><strong>{{ $client->client_name }}</strong></p>
            <p>{{ $client->client_address_one }}, {{ $client->client_city }}</p>
            <p>{{ $client->client_phonenumber }}</p>
            <p>{{ $client->client_email }}</p>
        </address>
    </header>
    <article>
        <p>To: </p>
        <address class="norm" style="margin-top: -1em">
            <h4 style="margin: 0; padding: 0; color:#0118e4;">{{ $invoice->customer->customer_firstname }}
                {{ $invoice->customer->customer_lastname }}</h4>
            <p style="margin: 0; padding: 0;">{{ $invoice->customer->customer_email }}</p>
            <p style="margin: 0; padding: 0;">{{ $invoice->customer->customer_address }}</p>
            <p style="margin: 0; padding: 0;">{{ $invoice->customer->phone }}</p>
            <p style="margin: 0; padding: 0;">Phone: {{ $invoice->customer->phone }}</p>
        </address>


        <table class="meta">
            <tr>
                <th><span>Invoice #</span></th>
                <td><span>{{ $invoice->invoice_no }}</span></td>
            </tr>
            <tr>
                <th><span>Date</span></th>
                <td><span>{{ $invoice->created_at->format('F j, Y') }}</span></td>
            </tr>
            <tr>
                <th><span>Amount Due</span></th>
                <td><span>Tsh</span><span>{{ number_format($invoice->total_amount, 2) }}</span></td>
            </tr>
        </table>

        <table class="inventory">
            <thead>
                <tr>
                    <th><span>S. No</span></th>
                    <th><span>Description</span></th>
                    <th><span>Load Name</span></th>
                    <th><span>Load Price</span></th>
                    <th><span>Amount</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->invoiceLoad as $invoiceLoad)
                    <tr>
                        <td><span>{{ $loop->iteration}}</span></td>
                        <td><span>{{ $invoiceLoad->loadpackage->load_description }}</span></td>
                        <td><span>{{ $invoiceLoad->loadpackage->load_name }}</span></td>
                        <td><span>{{ number_format($invoiceLoad->load_price, 2) }}</span></td>
                        <td><span>{{ number_format($invoiceLoad->load_price, 2) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="payment-info" style="font-size: 12px; margin-top: -1em;">
            <h2 style="margin: 0; padding: 0;">Payment Info</h2>
            <p><strong>Bank Name:</strong> NMB BANK (T) LTD</p>
            <p><strong>Acc Name:</strong> KODA.KODA INVESTMENT</p>
            <p><strong>Acc No:</strong> 62110028276</p><br>
            <p><strong>Bank Name:</strong> CRDB BANK (T) LTD</p>
            <p><strong>Acc Name:</strong>  KODA.KODA INVESTMENT</p>
            <p><strong>Acc No:</strong> 62110028276</p>
        </div>
        <table class="balance" style="margin-top: -1em">
            <tr>
                <th><span>Total</span></th>
                <td><span>{{ number_format($invoice->total_amount, 2) }}</span></td>
            </tr>
        </table>
    </article>
    <hr>
    <aside style="font-size: 12px; margin-top:-5em">
        <h4>Terms & Conditions</h4>
        <div>
            <p style="margin: 0; padding: 0;"><strong>Delivery Timeline:</strong> Products will be delivered within 7
                days of receiving the Local Purchase Order.</p>
            <p style="margin: 0; padding: 0;"><strong>Payment Terms:</strong> Payment is due 30 days after the receipt
                of the products.</p>
            <p style="margin: 0; padding: 0;"><strong>Delivery Location:</strong> Products will be delivered to the
                address specified in the LPO.</p>
        </div>
    </aside><br>
    <hr>
    <aside style="font-size: 12px">
        <div>
            <p style="margin: 0; padding: 0;">Golden Tulip Masaki, Coco beach Road, Masaki P.O. Box 1800
                Ubungo Dar es salaam, Tanzania | Tel: +255 710 459 064 Email: info@koda_koda.com</p>

        </div>
    </aside>
</body>

</html>
