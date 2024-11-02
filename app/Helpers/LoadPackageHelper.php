<?php

namespace App\Helpers;

use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\InvoiceLoad;
use App\Models\Customer;
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
        $invoice->total_amount = 0;
        $invoice->total_balance = 0;
        $invoice->total_transportfee = 0;
        $invoice->status = 0;
        $invoice->save();

        return $invoice;
    }

    public static function createLoad($validatedData, $index, $clientId, $customerId, $userId, $branchCode, $invoiceId)
    {
        $load_no = 'LOAD-' . $branchCode . '-' . date('Ymd') . '-' . strtoupper(Str::random(5));

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
        $load->container_id = $validatedData['container_id'][$index];
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

    public static function sendCustomerNotification($customerId, $load_no, $clientName, $load_weight_value, $load_price)
    {
        $customer = Customer::find($customerId);
        $message = "Hello {$customer->customer_firstname} {$customer->customer_lastname}, Tunapenda Kukujulisha kuwa mzigo wako kutoka CHINA- GUANGZHOU Tracking No - {$load_no} umepokelewa kwenye warehouse yetu CHINA- GUANGZHOU. Mzigo utaondoka haraka iwezekanavyo.Mzigo wako una {$load_weight_value}, utalipia {$load_price} baada ya Mzigo kufika Ofisini.Ahsante kwa Kusafirisha na {$clientName}";

        return sendMessage($customer->phone, $message);
    }
}
