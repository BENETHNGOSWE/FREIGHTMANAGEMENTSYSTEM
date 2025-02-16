<h3>admin</h3>

<?php
namespace App\Exports;

use App\Models\SalesReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesReportExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $currentRow = 4;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::parse($this->startDate)->endOfMonth();
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection()
    {
        $query = SalesReport::select(
            'staffs.id as staff_id',
            'staffs.staff_firstname as staff_name',
            'sales_reports.receipt',
            'customers.customer_firstname as customer_name',
            'sales_reports.date',
            'sales_reports.cbm',
            'sales_reports.nature',
            'sales_reports.hub',
            'sales_reports.amount1',
            'sales_reports.amount2'
        )
        ->leftJoin('customers', 'sales_reports.customer_id', '=', 'customers.id')
        ->leftJoin('staffs', 'sales_reports.staff_id', '=', 'staffs.id')
        ->whereNotNull('staffs.id')
        ->whereNotNull('staffs.staff_firstname')
        ->where('staffs.staff_firstname', '!=', '');

        if ($this->startDate) {
            $query->whereDate('sales_reports.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('sales_reports.date', '<=', $this->endDate);
        }

        return $query->orderBy('staffs.staff_firstname')
                    ->orderBy('sales_reports.date')
                    ->get()
                    ->groupBy('staff_name');
    }

    public function headings(): array
    {
        return [
            'Name',
            'Receipt',
            'Customer Name',
            'Date',
            'CBM',
            'Nature',
            'Hub',
            'Amount 1',
            'Amount 2'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get month and year from start date
        $monthYear = Carbon::parse($this->startDate)->format('F Y');
        
        // Add title with month and year
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', strtoupper($monthYear . ' CBM REPORT'));

        // Add date range
        $sheet->mergeCells('A2:I2');
        $dateRange = 'Date Range: ' . 
                    Carbon::parse($this->startDate)->format('d M Y') . 
                    ' to ' . 
                    Carbon::parse($this->endDate)->format('d M Y');
        $sheet->setCellValue('A2', $dateRange);

        // Style the header row (row 4)
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'bold' => true,
            ]
        ]);

        $currentRow = 5; // Start after headers
        $groupedData = $this->collection();

        foreach ($groupedData as $staffName => $staffGroup) {
            // Add staff header
            $sheet->mergeCells("A{$currentRow}:I{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", "Staff: " . $staffName);
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'CCCCCC'],
                ],
                'font' => ['bold' => true]
            ]);
            $currentRow++;

            $startRow = $currentRow;
            $totalCBM = 0;
            $totalAmount1 = 0;
            $totalAmount2 = 0;

            // Add all records for this staff member
            foreach ($staffGroup as $record) {
                $sheet->setCellValue("A{$currentRow}", $record->staff_name);
                $sheet->setCellValue("B{$currentRow}", $record->receipt);
                $sheet->setCellValue("C{$currentRow}", $record->customer_name);
                $sheet->setCellValue("D{$currentRow}", $record->date);
                $sheet->setCellValue("E{$currentRow}", $record->cbm);
                $sheet->setCellValue("F{$currentRow}", $record->nature);
                $sheet->setCellValue("G{$currentRow}", $record->hub);
                $sheet->setCellValue("H{$currentRow}", $record->amount1);
                $sheet->setCellValue("I{$currentRow}", $record->amount2);

                $totalCBM += floatval($record->cbm);
                $totalAmount1 += floatval($record->amount1);
                $totalAmount2 += floatval($record->amount2);
                
                $currentRow++;
            }

            // Add subtotal row with only the totals
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", "Subtotal");
            $sheet->setCellValue("E{$currentRow}", number_format($totalCBM, 2));
            $sheet->setCellValue("F{$currentRow}", "");
            $sheet->setCellValue("G{$currentRow}", "");
            $sheet->setCellValue("H{$currentRow}", number_format($totalAmount1, 0));
            $sheet->setCellValue("I{$currentRow}", number_format($totalAmount2, 0));
            
            // Style the subtotal row
            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EEEEEE'],
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                ]
            ]);

            $currentRow++;
            // Add a blank row for spacing
            $currentRow++;
        }

        // Make all columns auto-width
        foreach(range('A','I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Apply styles for title and date range
        return [
            1 => [ // Title row
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center']
            ],
            2 => [ // Date range row
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center']
            ]
        ];
    }
}