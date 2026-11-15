<?php

namespace App\Helpers;

use App\Models\Invoice;
use App\Models\Loadpackage;
use App\Models\InvoiceLoad;
use App\Models\Customer;
use App\Models\Clientactivename;
use App\Models\Sendername;
use App\Models\SystemSetting;
use Illuminate\Support\Str;
use DB;

class LoadPackageHelper
{
    /**
     * Is the customer SMS switch on for this client?
     *
     * The switch lives in `system_settings` (key `send_load_notifications`), so it survives
     * logins and is per client. When it is off nothing is sent, but every other operation
     * (saving the load, the invoice, the container, the reports) keeps working normally.
     */
    public static function smsEnabled(?int $clientId): bool
    {
        if (! $clientId) {
            return false;
        }

        try {
            return SystemSetting::notificationsEnabled($clientId);
        } catch (\Throwable $e) {
            // A settings/table problem must never stop a load from being created.
            \Log::warning('Could not read the SMS notification setting: ' . $e->getMessage());

            return true;
        }
    }

    /**
     * Send one SMS, never throwing.
     *
     * Returns ['success' => bool, 'skipped' => bool, 'message' => string]. A failed SMS is
     * logged, never allowed to roll back the business transaction that triggered it.
     */
    public static function sendSmsSafely(?int $clientId, ?string $phone, ?string $message, ?string $senderId): array
    {
        if (! self::smsEnabled($clientId)) {
            return ['success' => true, 'skipped' => true, 'message' => 'SMS notifications are switched off.'];
        }

        if (blank($phone) || blank($message)) {
            return ['success' => false, 'skipped' => true, 'message' => 'No phone number or message to send.'];
        }

        try {
            $response = sendMessage($phone, $message, $senderId);

            if (! ($response['success'] ?? false)) {
                \Log::warning('SMS not delivered to ' . $phone . ': ' . ($response['message'] ?? 'unknown error'));
            }

            return $response + ['skipped' => false];
        } catch (\Throwable $e) {
            \Log::warning('SMS failed for ' . $phone . ': ' . $e->getMessage());

            return ['success' => false, 'skipped' => false, 'message' => $e->getMessage()];
        }
    }

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

    if (! $customer) {
        return ['success' => false, 'skipped' => true, 'message' => 'Customer not found.'];
    }

    if (! self::smsEnabled((int) $customer->client_id)) {
        return ['success' => true, 'skipped' => true, 'message' => 'SMS notifications are switched off.'];
    }

    $clientActiveName = Clientactivename::where('client_id', $customer->client_id)->first();
    $senderName = Sendername::find($clientActiveName->sendername_id)->name ?? null;

    $message = "Habari {$customer->customer_firstname} {$customer->customer_lastname}, Mzigo wako (Tracking No: {$load_no})

Item: {$load_name}
Cartons: {$load_price}
Weight: {$load_weight_value} {$unittype_name}

Umepokelewa warehouse kwetu. Tutakujulisha Utakapoanza Safari.
Ahsante kwa kusafirisha na {$clientName}.";

    return self::sendSmsSafely((int) $customer->client_id, $customer->phone, $message, $senderName);
}


     public static function sendCustomerNotificationafterLoading($customerId, $load_no, $load_delivery_date, $container_number, $id)
    {
        $customer = Customer::find($customerId);

        if (! $customer) {
            return ['success' => false, 'skipped' => true, 'message' => 'Customer not found.'];
        }

        if (! self::smsEnabled((int) $customer->client_id)) {
            return ['success' => true, 'skipped' => true, 'message' => 'SMS notifications are switched off.'];
        }

        $clientActiveName = Clientactivename::where('client_id', $customer->client_id)->first();
        $senderName = Sendername::find($clientActiveName->sendername_id)->name ?? null;

        $message = "Habari {$customer->customer_firstname} {$customer->customer_lastname}, 
Mzigo wako (Tracking No: {$load_no}) umepakiliwa kwenye Kontena Namba: {$container_number}.
Uko tayari kuanza safari. 
Utawasili Bandari ya Dar-es-salaam tarehe {$load_delivery_date}.";
        return self::sendSmsSafely((int) $customer->client_id, $customer->phone, $message, $senderName);
    }



}


