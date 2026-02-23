<?php

namespace App\Exports\Master;

use App\Models\Master\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Supplier::whereNull('deleted_at')
            ->select('code','name','location','person_in_charge','contact')
            ->get();
    }

    public function headings(): array
    {
        return ['Code','Name','Location','PIC','Contact'];
    }
}
