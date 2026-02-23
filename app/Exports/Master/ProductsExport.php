<?php
namespace App\Exports\Master;

use App\Models\Master\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::whereNull('deleted_at')
            ->select('barcode', 'name', 'het', 'created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Barcode',
            'Name',
            'HET',
            'Created At'
        ];
    }
}