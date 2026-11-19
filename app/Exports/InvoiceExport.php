<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;

class InvoiceExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles, WithMapping
{
    protected $request;
    protected $startDate;
    protected $endDate;
    protected $clientId;

    public function __construct(Request $request, $clientId = null, $startDate = null, $endDate = null)
    {
        $this->request = $request;
        $this->clientId = $clientId ?? auth()->user()?->client_id;
        $this->startDate = $startDate ?? $request->start_date;
        $this->endDate = $endDate ?? $request->end_date;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection()
    {
        $query = Invoice::with('customer')->where('client_id', $this->clientId);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
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
        return ['Invoice Number', 'Customer Name', 'Amount', 'Status'];
    }

    public function map($invoice): array
    {
        return [$invoice->invoice_no, $invoice->customer ? strtoupper($invoice->customer->customer_firstname) : 'N/A', number_format($invoice->total_amount, 2), $this->getStatusText($invoice->status)];
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

    public function styles(Worksheet $sheet)
    {
        // Get the highest row
        $highestRow = $sheet->getHighestRow();
        $monthYear = $this->startDate ? Carbon::parse($this->startDate)->format('F Y') : Carbon::now()->format('F Y');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        // Title - Row 1
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', strtoupper($monthYear . ' INVOICE REPORT'));

        // Date Range - Row 2
        $sheet->mergeCells('A2:D2');
        $dateRange = ($this->startDate && $this->endDate)
        ? 'DATE RANGE: ' . Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y')
        : 'DATE RANGE: ALL DATES';
        $sheet->setCellValue('A2', strtoupper($dateRange));

        // Style the title row
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Style the date range row
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Style the headers row
        $sheet->getStyle('A4:D4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style data rows
        if ($highestRow > 4) {
            $sheet->getStyle('A5:D' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);

            // Right align amounts
            $sheet
                ->getStyle('C5:C' . $highestRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return $sheet;
    }
}
