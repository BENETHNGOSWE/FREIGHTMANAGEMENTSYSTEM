<?php

namespace App\Imports;

use App\Models\Customerlead;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class CustomerleadImport extends DefaultValueBinder implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $client_id;
    protected $user_id;

    public function __construct($client_id, $user_id)
    {
        $this->client_id = $client_id;
        $this->user_id = $user_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $phone = (string) $row['phone']; 
            $customer = Customerlead::where('phone', $phone)->first();

            if ($customer) {
                $customer->update([
                    'customer_firstname' => $row['customer_firstname'] ?? null,
                    'customer_middlename' => $row['customer_middlename'] ?? null,
                    'customer_lastname' => $row['customer_lastname'] ?? null,
                    'customer_email' => $row['customer_email'] ?? null,
                    'user_id' => $this->user_id,
                    'client_id' => $this->client_id,
                    'phone' => $phone,
                ]);
            } else {
                Customerlead::create([
                    'customer_firstname' => $row['customer_firstname'],
                    'customer_middlename' => $row['customer_middlename'],
                    'customer_lastname' => $row['customer_lastname'],
                    'customer_email' => $row['customer_email'],
                    'phone' => $phone,
                    'user_id' => $this->user_id,
                    'client_id' => $this->client_id,
                ]);
            }
        }
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && $cell->getColumn() === 'phone') { // Adjust 'phone' to match your Excel column header
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
