<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Invoice::with('customer');

        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }

        if ($this->request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) {
                $q->where('customer_firstname', 'like', '%' . $this->request->customer_name . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Customer Name',
            'Amount',
            'Status',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_no,
            $invoice->customer ? strtoupper($invoice->customer->customer_firstname) : 'N/A',
            number_format($invoice->total_amount, 2),
            ucfirst($invoice->status),
        ];
    }


    private function getStatusText($status)
    {
        $statusTexts = [
            0 => 'Pending',
            1 => 'Full Paid',
            2 => 'Partial Paid',
        ];

        return $statusTexts[$status] ?? 'Unknown';
    }
}
