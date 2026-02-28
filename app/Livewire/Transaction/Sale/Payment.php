<?php

namespace App\Livewire\Transaction\Sale;

use App\Models\Transaction\SalePayment;
use App\Models\User;
use App\Notifications\SaleOrderNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Transaction\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Payment extends Component
{
    use WithFileUploads;
    public Sale $sale;

    public $payment_date;
    public $amount;
    public $payment_method;
    public $notes;
    public $payment_proof;

    public $balanceFinal;
    public $paidTotal;
    public $previewProofPath = null;

    public function mount($id)
    {
        $this->sale = Sale::with([
            'patient',
            'payments'
        ])->findOrFail($id);

        $this->payment_date = now()->format('Y-m-d');
        $this->amount = 0;

        $this->paidTotal    = $this->sale->paid_total;
        $this->balanceFinal = $this->sale->balance;
    }

    public function updatedPaymentProof()
    {
        $this->validateOnly('payment_proof', [
            'payment_proof' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
    }

    public function savePayment()
    {
        try {
            $this->validate([
                'payment_date'   => 'required',
                'amount'         => 'required|numeric|min:1',
                'payment_method' => 'required',
                'payment_proof' => 'mimes:jpg,jpeg,png,pdf|max:4096',
            ]);

        } catch (ValidationException $e) {

            $this->dispatch('swal',
                icon: 'error',
                title: 'Validation Failed',
                text: collect($e->validator->errors()->all())->first()
            );

            throw $e; // supaya error tetap tercatat di dev
        }

        if ($this->amount > $this->balanceFinal) {
            $this->addError('amount', 'Payment exceeds remaining balance.');
            return;
        }

        DB::transaction(function () {
            $oldStatus = $this->sale->status;
            $proofPath = null;

            if ($this->payment_proof) {
                $proofPath = $this->payment_proof->store('payment-proofs', 'public');
            }

            $this->sale->payments()->create([
                'payment_date'   => $this->payment_date,
                'amount'         => $this->amount,
                'payment_method' => $this->payment_method,
                'notes'          => $this->notes,
                'payment_proof'  => $proofPath,
            ]);

            $paidTotal = $this->sale->payments()->sum('amount');
            $balance   = $this->sale->grand_total - $paidTotal;

            $status = $balance == 0 ? 'paid' : 'partial';

            $this->sale->update([
                'paid_total' => $paidTotal,
                'balance'    => $balance,
                'status'     => $status,
            ]);

            $this->paidTotal    = $paidTotal;
            $this->balanceFinal = $balance;

            // Jika status berubah
            if ($oldStatus !== $status) {

                if ($status === 'partial') {

                    $this->notifyAdmins(
                        $this->sale,
                        "Order {$this->sale->sale_number} dibayar sebagian oleh " . auth()->user()->name
                    );

                    $this->dispatch('refresh-notification');
                    $this->dispatch('play-sound');
                }

                if ($status === 'paid') {

                    $this->notifyAdmins(
                        $this->sale,
                        "Order {$this->sale->sale_number} telah dilunasi oleh " . auth()->user()->name
                    );

                    $this->dispatch('refresh-notification');
                    $this->dispatch('play-sound');
                }
            }
        });

        $this->reset(['amount', 'payment_method', 'notes', 'payment_proof']);

        $this->sale = Sale::find($this->sale->id);
    }

    protected function notifyAdmins($sale, $message)
    {
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new SaleOrderNotification($sale, $message));
        }
    }

    public function deletePayment($paymentId)
    {
        DB::transaction(function () use ($paymentId) {

            $payment = SalePayment::findOrFail($paymentId);

            if ($payment->sale_id !== $this->sale->id) {
                abort(403);
            }

            $payment->delete();

            // hitung ulang dari DB
            $paidTotal = SalePayment::where(
                'sale_id',
                $this->sale->id
            )->sum('amount');

            $balance = $this->sale->grand_total - $paidTotal;

            $status = 'posted';
            if ($paidTotal > 0) {
                $status = $balance == 0 ? 'paid' : 'partial';
            }

            $this->sale->update([
                'paid_total' => $paidTotal,
                'balance'    => $balance,
                'status'     => $status,
            ]);

            // update reactive property
            $this->paidTotal    = $paidTotal;
            $this->balanceFinal = $balance;

            // update instance status agar badge reactive
            $this->sale->status = $status;
        });
    }

    public function previewProof($path)
    {
        $this->previewProofPath = $path;

        $this->dispatch('open-proof-modal');
    }

    public function render()
    {
        return view('livewire.transaction.sale.payment')
            ->layout('layouts.app', [
                'title' => 'Sale Payment',
                'subtitle' => 'Manage payment only',
            ]);
    }
}