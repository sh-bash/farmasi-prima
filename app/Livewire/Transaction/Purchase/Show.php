<?php

namespace App\Livewire\Transaction\Purchase;

use Livewire\Component;
use App\Models\Transaction\Purchase;

class Show extends Component
{
    public Purchase $purchase;

    public function mount($id)
    {
        $this->purchase = Purchase::with([
            'supplier',
            'details.product'
        ])->findOrFail($id);
    }

    public function getSubtotalProperty()
    {
        return $this->purchase->details->sum('total');
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal
            - (float) $this->purchase->discount
            + (float) $this->purchase->tax;
    }

    public function render()
    {
        return view('livewire.transaction.purchase.show')
            ->layout('layouts.app', [
                'title' => 'Purchase Detail',
                'subtitle' => 'View purchase transaction',
            ]);
    }
}