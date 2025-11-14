<?php

namespace App\Helpers;

use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\InvoiceLoad;
use App\Models\Customer;
use App\Models\Clientactivename;
use App\Models\Sendername;
use Illuminate\Support\Str;
use DB;

class LoadPackageHelper
{
    public static function generateInvoice($customerId, $clientId)
    {
        $invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $invoice = new Invoice();
        $invoice->invoice_no = $invoice_no;
        $invoice->prefix = 'LOAD';
        $invoice->customer_id = $customerId;
        $invoice->client_id = $clientId;
        $invoice->user_id = auth()->id();
        $invoice->total_amount = 0;
        $invoice->total_balance = 0;
        $invoice->total_transportfee = 0;
        $invoice->status = 0;
        $invoice->save();

        return $invoice;
    }

    public static function createLoad($validatedData, $index, $clientId, $customerId, $userId, $branchCode, $invoiceId)
    {
        $randomString = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 7));
        $load_no = $branchCode . $randomString;

        $load = new Loadpackage();
        $load->client_id = $clientId;
        $load->customer_id = $customerId;
        $load->user_id = $userId;
        $load->load_no = $load_no;
        $load->load_name = $validatedData['load_name'][$index];
        $load->load_trucking_number = $validatedData['load_trucking_number'][$index];
        $load->load_description = $validatedData['load_description'][$index];
        $load->load_price = $validatedData['load_price'][$index];
        $load->load_transport_date = $validatedData['load_transport_date'][$index];
        $load->load_delivery_date = $validatedData['load_delivery_date'][$index];

        $load->load_length = $validatedData['load_length'][$index];
        $load->load_width = $validatedData['load_width'][$index];
        $load->load_height = $validatedData['load_height'][$index];
        $load->load_volume = $load->load_length * $load->load_width * $load->load_height;

        $load->container_id = $validatedData['is_ocean'][$index] ? $validatedData['container_id'][$index] ?? null : null;
        $load->load_weight_value = $validatedData['load_weight_value'][$index];
        $load->loadpackagetype_id = $validatedData['loadpackagetype_id'][$index];
        $load->unittype_id = $validatedData['unittype_id'][$index];
        $load->save();

        InvoiceLoad::create([
            'loadpackage_id' => $load->id,
            'invoice_id' => $invoiceId,
            'client_id' => $clientId,
            'load_price' => $load->load_price,
        ]);

        return $load;
    }

    public static function updateInvoiceTotals($invoice, $load)
    {
        $invoice->total_amount += $load->load_price;
        $invoice->total_transportfee += $load->load_transportfee;
        $invoice->total_balance += $load->load_price;
        $invoice->save();
    }

    public static function sendCustomerNotification(
    $customerId, 
    $load_no, 
    $clientName, 
    $load_weight_value, 
    $load_price, 
    $load_name, 
    $unittype_name, 
    $id
) {
    $customer = Customer::find($customerId);

    $clientActiveName = Clientactivename::where('client_id', $customer->client_id)->first();
    $senderName = Sendername::find($clientActiveName->sendername_id)->name;

    $message = "Habari {$customer->customer_firstname} {$customer->customer_lastname}, Mzigo wako (Tracking No: {$load_no})

Item: {$load_name}
Cartons: {$load_price}
Weight: {$load_weight_value} {$unittype_name}

Umepokelewa warehouse kwetu. Tutakujulisha Utakapoanza Safari.
Ahsante kwa kusafirisha na {$clientName}.";

    return sendMessage($customer->phone, $message, $senderName);
}


     public static function sendCustomerNotificationafterLoading($customerId, $load_no, $load_delivery_date, $container_number, $id)
    {
        $customer = Customer::find($customerId);
        $clientActiveName = Clientactivename::where('client_id', $customer->client_id)->first();
        $senderName = Sendername::find($clientActiveName->sendername_id)->name;

        $message = "Habari {$customer->customer_firstname} {$customer->customer_lastname}, 
Mzigo wako (Tracking No: {$load_no}) umepakiliwa kwenye Kontena Namba: {$container_number}.
Uko tayari kuanza safari. 
Utawasili Bandari ya Dar-es-salaam tarehe {$load_delivery_date}.";
        return sendMessage($customer->phone, $message, $senderName);
    }
}


