<?php

namespace App\Exports\Master;

use App\Models\Master\Supplier;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliersTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['code','name','location','contact','person_in_charge'];
    }

    public function array(): array
    {
        return [
            ['SUP001','PT Pharma Jaya','Jakarta','08123456789','Budi'],
            ['SUP002','CV Medika','Bandung','0822334455','Andi'],
        ];
    }
}
