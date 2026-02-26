<?php

namespace App\Livewire\Transaction\Purchase;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Purchase;

class Payment extends Component
{
    public $purchase;
    public $amount;
    public $payment_date;
    public $method;
    public $reference;

    public function mount($id)
    {
        $this->purchase = Purchase::findOrFail($id);

        if ($this->purchase->status === 'paid') {
            abort(403, 'Purchase already paid.');
        }

        $this->payment_date = now()->format('Y-m-d');
    }

    public function save()
    {
        DB::transaction(function () {

            $this->purchase->payments()->create([
                'payment_date' => $this->payment_date,
                'amount' => $this->amount,
                'method' => $this->method,
                'reference' => $this->reference,
            ]);

            $this->purchase->recalcTotals();
        });

        return redirect()->route('purchase.index');
    }

    public function render()
    {
        return view('livewire.transaction.purchase.payment');
    }
}