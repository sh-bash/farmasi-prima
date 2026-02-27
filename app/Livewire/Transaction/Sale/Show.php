<?php

namespace App\Livewire\Transaction\Sale;

use Livewire\Component;
use App\Models\Transaction\Sale;

class Show extends Component
{
    public Sale $sale;
    public $previewProofPath = null;


    public function mount($id)
    {
        $this->sale = Sale::with([
            'patient',
            'details.product',
            'payments'
        ])->findOrFail($id);
    }

    public function getSubtotalProperty()
    {
        return $this->sale->details->sum('total');
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal
            - (float) $this->sale->discount
            + (float) $this->sale->tax;
    }

    public function previewProof($path)
    {
        $this->previewProofPath = $path;

        $this->dispatch('open-proof-modal');
    }

    public function render()
    {
        return view('livewire.transaction.sale.show')
            ->layout('layouts.app', [
                'title' => 'Sale Detail',
                'subtitle' => 'View sale transaction',
            ]);
    }
}