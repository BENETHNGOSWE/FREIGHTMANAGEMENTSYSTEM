<?php

namespace App\Exports;

use App\Models\OfficeExpense;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OfficeExpenseExport implements FromCollection, WithHeadings, WithCustomStartCell, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $categoryId;

    public function __construct($startDate = null, $endDate = null, $categoryId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function collection()
    {
        $query = OfficeExpense::select(
            'office_expenses.expenses_description',
            'office_expenses.expenses_cost',
            'office_expense_categories.name as category_name',
            'office_expenses.status',
            'office_expenses.created_at'
        )
        ->leftJoin('office_expense_categories', 'office_expenses.office_expense_categories_id', '=', 'office_expense_categories.id');

        if ($this->startDate) {
            $query->whereDate('office_expenses.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('office_expenses.created_at', '<=', $this->endDate);
        }

        if ($this->categoryId) {
            $query->where('office_expenses.office_expense_categories_id', $this->categoryId);
        }

        $expenses = $query->get();
        
        // Map the collection to convert the status integer to a string
        $mappedExpenses = $expenses->map(function ($expense) {
            $statusString = '';
            switch ($expense->status) {
                case 0:
                    $statusString = 'Pending';
                    break;
                case 1:
                    $statusString = 'Approved';
                    break;
                case 2:
                    $statusString = 'Not Approved';
                    break;
                default:
                    $statusString = 'Unknown';
                    break;
            }
            // Update the status property to be the new string value
            $expense->status = $statusString;
            return $expense;
        });

        $totalCost = $mappedExpenses->sum('expenses_cost');
        
        // Add a new row for the total at the end of the collection
        $mappedExpenses->push((object) [
            'expenses_description' => 'TOTAL',
            'expenses_cost' => $totalCost,
            'category_name' => '',
            'status' => '',
            'created_at' => ''
        ]);

        return $mappedExpenses;
    }

    public function headings(): array
    {
        return [
            'Description',
            'Expense Cost',
            'Expense Category',
            'Status',
            'Date'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'OFFICE EXPENSES REPORT');

        $sheet->mergeCells('A2:E2');
        $dateRange = 'DATE RANGE: ' . 
                     ($this->startDate ? Carbon::parse($this->startDate)->format('d M Y') : 'N/A') . 
                     ' to ' . 
                     ($this->endDate ? Carbon::parse($this->endDate)->format('d M Y') : 'N/A');
        $sheet->setCellValue('A2', $dateRange);
        
        $sheet->getStyle('A' . $highestRow . ':E' . $highestRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0'],
            ],
        ]);
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center']
            ],
            2 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center']
            ],
            4 => [ 
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true,
                ]
            ]
        ];
    }
}
