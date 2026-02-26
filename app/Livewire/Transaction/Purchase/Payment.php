<?php

namespace App\Livewire\Transaction\Purchase;

use App\Models\Transaction\PurchasePayment;
use Livewire\Component;
use App\Models\Transaction\Purchase;
use Illuminate\Support\Facades\DB;

class Payment extends Component
{
    public Purchase $purchase;

    public $payment_date;
    public $amount;
    public $method;
    public $reference;
    public $balanceFinal;
    public $paidTotal;

    public function mount($id)
    {
        $this->purchase = Purchase::with([
            'supplier',
            'payments'
        ])->findOrFail($id);

        $this->payment_date = now()->format('Y-m-d');
        $this->amount = 0;

        $this->paidTotal    = $this->purchase->paid_total;
        $this->balanceFinal = $this->purchase->balance;
    }

    public function savePayment()
    {
        $this->validate([
            'payment_date' => 'required',
            'amount'       => 'required|numeric|min:1',
            'method'       => 'required',
        ]);

        if ($this->amount > $this->balanceFinal) {
            $this->addError('amount', 'Payment exceeds remaining balance.');
            return;
        }

        DB::transaction(function () {

            $this->purchase->payments()->create([
                'payment_date' => $this->payment_date,
                'amount'       => $this->amount,
                'method'       => $this->method,
                'reference'    => $this->reference,
            ]);

            $paidTotal = $this->purchase->payments()->sum('amount');
            $balance   = $this->purchase->grand_total - $paidTotal;

            $status = $balance == 0 ? 'paid' : 'partial';

            $this->purchase->update([
                'paid_total' => $paidTotal,
                'balance'    => $balance,
                'status'     => $status,
            ]);

            $this->paidTotal    = $paidTotal;
            $this->balanceFinal = $balance;
        });

        $this->reset(['amount', 'method', 'reference']);
        $this->purchase = Purchase::find($this->purchase->id);
        // $this->purchase->refresh();
    }

    public function render()
    {
        return view('livewire.transaction.purchase.payment')
            ->layout('layouts.app', [
                'title' => 'Purchase Payment',
                'subtitle' => 'Manage payment only',
            ]);
    }
}