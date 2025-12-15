<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Invoice</title>
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

            word-wrap: break-word;
            overflow-wrap: break-word;
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
            border-radius: 1px;
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
        }

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
        }

        table.inventory th:first-child {
            width: 50px;
        }

        /* table.inventory th:nth-child(2) {
            width: 300px;
        } */

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

       .inventory th:nth-child(1),
        .inventory td:nth-child(1) {
            width: 7%; 
        }

        .inventory th:nth-child(2),
        .inventory td:nth-child(2) {
            width: 55%; 
        }

        .inventory th:nth-child(3),
        .inventory td:nth-child(3) {
            width: 15%; 
        }

        .inventory th:nth-child(4),
        .inventory td:nth-child(4) {
            width: 15%; 
        }

        .inventory th:nth-child(5),
        .inventory td:nth-child(5) {
            width: 16%; 
            text-align: center;
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


         .badge {
            display: inline-block;
            padding: 1em 0.9em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        
        /* New styles for signature sections */
        .signature-section {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .signature-section:last-child {
            border-bottom: none;
        }
        
        .signature-section p {
            margin: 3px 0 !important;
        }
        
        .role-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px !important;
        }
    </style>
</head>

<body>
    <div class="page-border">
        <header>
            <h1>Expenses Invoice</h1>
            <div class="company-header">
                <div class="company-logo">
                    @if ($client->client_logo)
                        <img alt="logko" src="{{ public_path('storage/uploads/' . $client->client_logo) }}" style="width: 150px; height: auto;">
                    @endif
                </div>               
                <div class="company-info">
                    <div class="company-name">{{ strtoupper($client->client_name) }}</div>
                    <div class="company-details">
                        Office Address: {{ strtoupper($client->client_address_one) }}<br>
                        Office Phone: {{ strtoupper($client->client_phonenumber) }}<br>
                        TIN Number: {{ strtoupper($client->client_tinnumber) }}<br>
                        {{ strtoupper($client->client_city . ' ' . $client->client_country) }}
                    </div>
                </div>
            </div>
        </header>
        <h3 style="text-align:center; margin-top:-1em">Invoice Details</h3>
        <hr>
        <article>
            <table class="inventory">
                
                <thead>
                    <tr>
                        <th><span>S. No</span></th>
                        <th><span>Expenses Description</span></th>
                        <th><span>Expenses Cost</span></th>
                        <th><span>Expense Category</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span>1</span></td> 
                        <td><span>{{ $expenseinvoice->expenses_description }}</span></td> 
                        <td><span>{{ $expenseinvoice->expenses_cost }}</span></td> 
                        <td><span>{{ $expenseinvoice->categoryexpense->name }}</span></td> 
                    </tr>
                </tbody>
            </table>
        </article>
        <header>
            <span style="margin-top: -1em; font-size: 10px">
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">Approved By:</p>
                
                <!-- Managing Director -->
                <div class="signature-section">
                    <p class="role-title">Managing Director (MD)</p>
                    <p>Signature: _____________________</p>
                    <p>Name: _____________________</p>
                    <p>Date: _____________________</p>
                </div>
                
                  <!-- General Manager -->
                <div class="signature-section">
                    <p class="role-title">General Manager (GM)</p>
                    <p>Signature: _____________________</p>
                    <p>Name: _____________________</p>
                    <p>Date: _____________________</p>
                </div>
                
                <!-- Accountant -->
                <div class="signature-section">
                    <p class="role-title">Accountant</p>
                    <p>Signature: _____________________</p>
                    <p>Name: _____________________</p>
                    <p>Date: _____________________</p>
                </div>
                
                <!-- Cashier -->
                <div class="signature-section">
                    <p class="role-title">Cashier</p>
                    <p>Signature: _____________________</p>
                    <p>Name: _____________________</p>
                    <p>Date: _____________________</p>
                </div>
                
                <!-- Receiver -->
                <div class="signature-section">
                    <p class="role-title">Receiver</p>
                    <p>Signature: _____________________</p>
                    <p>Name: _____________________</p>
                    <p>Date: _____________________</p>
                </div>
            </span>
            <address style="margin-top: -1em; font-size: 10px">
                <p style="margin: 0; font-weight: bold;">Printed By:</p>
                <p style="margin: 5px 0;">Name: <strong>{{ auth()->user()->name }}</strong></p>
                <p style="margin: 5px 0;">Date:
                    <strong>{{ \Carbon\Carbon::now('Africa/Nairobi')->format('F j, Y, g:i A') }}</strong></p>
            </address>
        </header>



        <footer>
            <aside class="footer-left" style="display: inline-block; text-align: left; font-size: 12px">
                <div>
                    <p style="margin: 0; padding: 0;">{{ $client->client_address_one}} P.O. Box {{$client->client_fax}}
                        {{$client->client_city}}, {{$client->client_country}} | Tel: {{$client->client_phonenumber}} Email: {{ $client->client_email}}</p>

                </div>
            </aside>
           
        </footer>
    </div>
</body>

</html>