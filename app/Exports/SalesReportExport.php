<?php

namespace App\Exports;

use App\Models\SalesReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class SalesReportExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function startCell(): string
    {
        return 'A4';   
    }
    public function collection()
    {
        $query = SalesReport::select(
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
            ->orderBy('staffs.staff_firstname')
            ->orderBy('sales_reports.date');
    
        if ($this->startDate) {
            $query->whereDate('sales_reports.date', '>=', $this->startDate);
        }
    
        if ($this->endDate) {
            $query->whereDate('sales_reports.date', '<=', $this->endDate);
        }
    
        $records = $query->get();
    
        $groupedData = new Collection();
        $currentStaff = null;
        $totalAmount1 = 0;
        $totalAmount2 = 0;
    
        foreach ($records as $record) {
            if ($currentStaff !== $record->staff_name) {
                if ($groupedData->isNotEmpty() && $currentStaff !== null) {
                    // Insert subtotal row before switching to the next staff
                    $groupedData->push(collect(['', '', '', '', '', 'Subtotal', '', $totalAmount1, $totalAmount2]));
                    $groupedData->push(collect(['', '', '', '', '', '', '', '', '']));
                }
    
                // Format the staff name with "Staff Name: "
                $groupedData->push(collect([
                    'NAME: ' . strtoupper($record->staff_name), '', '', '', '', '', '', '', ''
                ]));
    
                // Reset totals
                $currentStaff = $record->staff_name;
                $totalAmount1 = 0;
                $totalAmount2 = 0;
            }
    
            // Add up totals
            $totalAmount1 += $record->amount1;
            $totalAmount2 += $record->amount2;
    
            // Push actual sales record
            $groupedData->push($record);
        }
    
        // Insert the last staff's subtotal row
        if ($groupedData->isNotEmpty()) {
            $groupedData->push(collect(['', '', '', '', '', 'Subtotal', '', $totalAmount1, $totalAmount2]));
        }
    
        return $groupedData;
    }
    
    
    

    public function headings(): array
    {
        return array_map('strtoupper', [
            'Name',
            'Receipt',
            'Customer Name',
            'Date',
            'CBM',
            'Nature',
            'Hub',
            'Amount 1',
            'Amount 2'
        ]);
    }

    public function styles(Worksheet $sheet)
    {
       $monthYear = Carbon::parse($this->startDate)->format('F Y');
        
       // Add title with month and year
       $sheet->mergeCells('A1:I1');
       $sheet->setCellValue('A1', strtoupper($monthYear . ' CBM REPORT'));

       // Add date range
       $sheet->mergeCells('A2:I2');
       $dateRange = 'DATE RANGE: ' . 
                    strtoupper(Carbon::parse($this->startDate)->format('d M Y')) . 
                    ' to ' . 
                    strtoupper(Carbon::parse($this->endDate)->format('d M Y'));
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


       $highestRow = $sheet->getHighestRow();
    
       for ($row = 5; $row <= $highestRow; $row++) {
           $cellValue = $sheet->getCell("A$row")->getValue();
   
           if (!empty($cellValue) && $sheet->getCell("B$row")->getValue() == '') {
               // Apply bold styling for staff names
               $sheet->getStyle("A$row")->applyFromArray([
                   'font' => ['bold' => true, 'size' => 12],
               ]);
           }

           if ($sheet->getCell("F$row")->getValue() == 'Subtotal') {
            // Apply bold styling for subtotal row
            $sheet->getStyle("A$row:I$row")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
            ]);
        }
       }

        // Apply styles
        return [
            1 => [ // Title row
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center']
            ],
            2 => [ // Date range row
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center']
            ],
            4 => [ // Headers row (bold)
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'center']
            ]
        ];
    }
}
