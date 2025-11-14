<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerLoadTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // You can include one sample row or leave empty
        return [
            [
                'John', 'M', 'Doe', 'john@example.com', '0712345678',
                'Container 1', 'TRK123', 'Electronics', 1, '100000',
                '2025-11-08', '2025-11-12', '200', 2, 1, 'ocean'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'customer_firstname',
            'customer_middlename',
            'customer_lastname',
            'customer_email',
            'phone',
            'load_name',
            'load_trucking_number',
            'load_description',
            'loadpackagetype_id',
            'load_price',
            'load_transport_date',
            'load_delivery_date',
            'load_weight_value',
            'unittype_id',
            'is_ocean',
            'mode',
        ];
    }
}

