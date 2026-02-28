<?php

namespace App\Livewire\Report\Payable;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Supplier;

class Detail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $supplier;
    public $supplierId;

    public function mount($supplierId)
    {
        $this->supplierId = $supplierId;
        $this->supplier = Supplier::findOrFail($supplierId);
    }

    public function getDataProperty()
    {
        return DB::table('purchases')
            ->where('supplier_id', $this->supplierId)
            ->where('balance', '>', 0)
            ->select(
                'id',
                'purchase_date',
                'purchase_number',
                'grand_total',
                'paid_total',
                'balance',
                'status'
            )
            ->orderByDesc('purchase_date')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.report.payable.detail', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Payable Detail',
            'subtitle' => 'Outstanding supplier invoices'
        ]);
    }
}