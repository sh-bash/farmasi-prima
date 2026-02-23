<?php
namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductsTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'barcode',
            'name',
            'het'
        ];
    }

    public function array(): array
    {
        return [
            [
                '1001',
                'Paracetamol',
                '5000'
            ],
            [
                '1002',
                'Amoxicillin',
                '12000'
            ]
        ];
    }

    public function title(): string
    {
        return 'Template';
    }
}