<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Staff;
use App\Helpers\LoadPackageHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CustomerLoadImport implements ToModel, WithHeadingRow
{
    private const DEFAULT_PASSWORD = '123456';

    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            $row['phone'] = $this->normalizePhoneNumber($row['phone']);
            $userId = auth()->user()->id;
            $clientId = auth()->user()->client_id;
            
            $staff = Staff::where('user_id', $userId)->first();
            $branchCode = $staff ? $staff->branch->branch_code : 'TZ';
            
            $customer = $this->findOrCreateCustomer($row, $clientId);
            $invoice = $this->findOrCreateInvoice($customer->id, $clientId);
            $validatedData = $this->prepareLoadData($row);
            
            $load = LoadPackageHelper::createLoad(
                $validatedData,
                0,
                $clientId,
                $customer->id,
                $userId,
                $branchCode,
                $invoice->id
            );

            LoadPackageHelper::updateInvoiceTotals($invoice, $load);

            DB::commit();
            
            return $load;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import error on row: " . json_encode($row) . " | " . $e->getMessage());
            throw $e;
        }
    }

    private function normalizePhoneNumber($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if(strlen($phone) == 9 && !str_starts_with($phone, '+255')) {
            $phone = '+255' . $phone;
        }

        return $phone;
    }

    private function findOrCreateCustomer(array $row, int $clientId): Customer
    {
        $customer = Customer::where('client_id', $clientId)
            ->where(function($query) use ($row) {
                $query->where('customer_email', $row['customer_email'])
                      ->orWhere('phone', $row['phone']);
            })
            ->first();

        if ($customer) {
            return $customer;
        }

        $user = User::where('email', $row['customer_email'])
            ->orWhere('phone', $row['phone'])
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => trim("{$row['customer_firstname']} {$row['customer_lastname']}"),
                'email' => $row['customer_email'],
                'phone' => $row['phone'],
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'client_id' => $clientId,
            ]);
        }

        return Customer::create([
            'user_id' => $user->id,
            'customer_no' => createcustomeridNumber($clientId),
            'client_id' => $clientId,
            'customer_firstname' => $row['customer_firstname'],
            'customer_middlename' => $row['customer_middlename'] ?? null,
            'customer_lastname' => $row['customer_lastname'],
            'customer_email' => $row['customer_email'],
            'phone' => $row['phone'],
        ]);
    }

    private function findOrCreateInvoice(int $customerId, int $clientId): Invoice
    {
        $invoice = Invoice::where('customer_id', $customerId)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        return $invoice ?? LoadPackageHelper::generateInvoice($customerId, $clientId);
    }

    private function prepareLoadData(array $row): array
    {
        return [
            'load_name' => [$row['load_name']],
            'load_trucking_number' => [$row['load_trucking_number']],
            'load_description' => [$row['load_description'] ?? null],
            'loadpackagetype_id' => [$row['loadpackagetype_id']],
            'load_price' => [$row['load_price']],
            'load_transport_date' => [$this->formatDate($row['load_transport_date'])],
            'load_delivery_date' => [$this->formatDate($row['load_delivery_date'])],
            'load_weight_value' => [$row['load_weight_value']],
            'unittype_id' => [$row['unittype_id']],
            'is_ocean' => [$row['is_ocean'] ?? 0],
            'container_id' => [$row['container_id'] ?? null],
            'load_length' => [$row['load_length'] ?? 1],
            'load_width' => [$row['load_width'] ?? 1],
            'load_height' => [$row['load_height'] ?? 1],
        ];
    }

    private function formatDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return date('Y-m-d', strtotime($value));
    }
}