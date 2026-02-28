<?php

namespace App\Livewire\Report\Receivable;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Patient;

class Detail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $patient;
    public $patientId;

    public function mount($patientId)
    {
        $this->patientId = $patientId;
        $this->patient = Patient::findOrFail($patientId);
    }

    public function getDataProperty()
    {
        return DB::table('sales')
            ->where('patient_id', $this->patientId)
            ->where('balance', '>', 0)
            ->select(
                'id',
                'sale_date',
                'sale_number',
                'grand_total',
                'paid_total',
                'balance',
                'status'
            )
            ->orderByDesc('sale_date')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.report.receivable.detail', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Receivable Detail',
            'subtitle' => 'Outstanding invoices'
        ]);
    }
}