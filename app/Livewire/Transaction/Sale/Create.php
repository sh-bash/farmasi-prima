<?php

namespace App\Livewire\Transaction\Sale;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Sale;
use App\Notifications\SaleOrderNotification;
use Livewire\WithFileUploads;
use App\Helpers\StockHelper;


class Create extends Component
{
    use WithFileUploads;
    public $patient_id;
    public $sale_date;
    public $notes;

    public $discount = 0;
    public $tax = 0;

    public $items = [];
    public $payments = [];
    public $stockAvailable = [];
    public $isPatient = false;


    protected $listeners = [
        'selectProduct',
        'setPatient'
    ];

    /* =========================================
       MOUNT
    ==========================================*/

    public function mount()
    {
        $this->sale_date = now()->format('Y-m-d');

        $user = auth()->user();

        if ($user->hasRole('patient')) {

            $this->isPatient = true;

            // 🔥 langsung set patient_id milik user login
            $this->patient_id = $user->patient?->id;
        }
    }

    /* =========================================
       PATIENT
    ==========================================*/

    public function setPatient($data)
    {
        $this->patient_id = $data['id'];
    }

    /* =========================================
       DETAIL ROW
    ==========================================*/

    public function addRow()
    {
        $this->items[] = [
            'product_id' => null,
            'qty' => 1,
            'price' => 0,
            'discount' => 0,
            'total' => 0,
        ];

        $this->dispatch('reinit-select2');
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /* =========================================
       PAYMENT ROW
    ==========================================*/

    public function addPaymentRow()
    {
        $this->payments[] = [
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 0,
            'method' => '',
            'reference' => '',
            'payment_proof'=> null,
        ];
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function getTotalPaymentProperty()
    {
        return collect($this->payments)->sum('amount');
    }

    public function getBalanceProperty()
    {
        return $this->grandTotal - $this->totalPayment;
    }

    /* =========================================
       PRODUCT SELECT (SELECT2)
    ==========================================*/

    public function selectProduct($data)
    {
        $index = $data['index'];

        $this->items[$index]['product_id'] = $data['id'];
        $this->items[$index]['product_name'] = $data['text'];
        $this->items[$index]['price'] = $data['price'];
        $this->items[$index]['qty'] = 1;
        $this->items[$index]['discount'] = 0;

        // Ambil stock
        $stock = StockHelper::getCurrentStock($data['id']);
        $this->stockAvailable[$index] = $stock < 0 ? 0 : $stock;

        $this->recalculateRow($index);
    }

    /* =========================================
       AUTO CALCULATION
    ==========================================*/

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);

        if (count($parts) >= 3) {
            $index = $parts[1];
            $this->recalculateRow($index);
        }
    }

    public function recalculateRow($index)
    {
        $qty = (int) ($this->items[$index]['qty'] ?? 0);
        $price = (float) ($this->items[$index]['price'] ?? 0);
        $discount = (float) ($this->items[$index]['discount'] ?? 0);

        $available = $this->stockAvailable[$index] ?? 0;

        // VALIDASI STOCK REALTIME
        if ($qty > $available) {

            $this->dispatch('swal',
                icon: 'error',
                title: 'Stock Not Enough',
                text: 'Available stock: ' . $available
            );

            $this->items[$index]['qty'] = $available < 0 ? 0 : $available;
            $qty = $available;
        }

        $this->items[$index]['total'] = ($qty * $price) - $discount;
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum('total');
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal - floatval($this->discount) + floatval($this->tax);
    }

    public function updatedPayments($value, $key)
    {
        $parts = explode('.', $key);

        if (count($parts) === 3 && $parts[2] === 'method') {

            $index = $parts[1];

            if ($value === 'bpjs') {

                // Set amount otomatis = sisa balance
                $this->payments[$index]['amount'] = $this->balance;
            }
        }
    }

    /* =========================================
       SAVE
    ==========================================*/

    public function save()
    {

        $user = auth()->user();

        // 🔐 Force patient_id jika login sebagai patient
        if ($user->hasRole('patient')) {
            $this->patient_id = $user->patient?->id;
        }

        // =========================
        // Dynamic Validation
        // =========================
        $rules = [
            'sale_date' => 'required|date',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'payments.*.payment_proof' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
        ];

        // hanya admin/staff wajib pilih patient
        if (!$user->hasRole('patient')) {
            $rules['patient_id'] = 'required';
        }

        $this->validate($rules);

        $isBpjs = collect($this->payments)
            ->contains(fn($p) => $p['method'] === 'bpjs');

        if (!$isBpjs) {

            foreach ($this->payments as $payment) {

                if ($payment['amount'] <= 0) {
                    $this->dispatch('swal',
                        icon: 'error',
                        title: 'Validation Failed',
                        text: 'Masak bayar 0 Rupiah bos'
                    );
                    return;
                }
            }

            if ($this->totalPayment > $this->grandTotal) {
                $this->dispatch('swal',
                    icon: 'error',
                    title: 'Validation Failed',
                    text: 'kebanyakan bos, kita gk butuh sedekah'
                );
                return;
            }
        }

        foreach ($this->items as $index => $item) {

            $currentStock = StockHelper::getCurrentStock($item['product_id']);

            if ($item['qty'] > $currentStock) {

                $this->dispatch('swal',
                    icon: 'error',
                    title: 'Stock Validation Failed',
                    text: 'Product stock has changed. Available: ' . $currentStock
                );

                return;
            }
        }

        DB::transaction(function () {
            if (auth()->user()->hasRole('patient')) {
                $this->patient_id = auth()->user()->patient?->id;
            }

            $paidTotal = $this->totalPayment;
            $balance   = $this->grandTotal - $paidTotal;

            $status = 'posted';

            if ($paidTotal > 0) {
                $status = $balance == 0 ? 'paid' : 'partial';
            }

            $sale = Sale::create([
                'sale_number' => 'SO-' . now()->format('YmdHis'),
                'sale_date' => $this->sale_date,
                'patient_id' => $this->patient_id,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'grand_total' => $this->grandTotal,
                'paid_total' => $paidTotal,
                'balance' => $balance,
                'status' => $status,
                'notes' => $this->notes,
            ]);

            foreach ($this->items as $item) {
                $sale->details()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'total' => $item['total'],
                ]);
            }

            $isBpjs = false;

            foreach ($this->payments as $payment) {

                /* =========================
                BPJS → Langsung Lunas
                ==========================*/
                if ($payment['method'] === 'bpjs') {

                    $sale->payments()->create([
                        'payment_date'   => $payment['payment_date'],
                        'amount'         => $sale->grand_total,
                        'payment_method' => 'bpjs',
                        'notes'          => $payment['reference'] ?? 'BPJS Payment',
                    ]);

                    $isBpjs = true;
                    break; // stop, tidak perlu payment lain
                }

                $proofPath = null;

                if (!empty($payment['payment_proof'])) {
                    $proofPath = $payment['payment_proof']
                        ->store('payment_proofs', 'public');
                }


                /* =========================
                Payment Normal
                ==========================*/
                if ($payment['amount'] > 0) {

                    $sale->payments()->create([
                        'payment_date'   => $payment['payment_date'],
                        'amount'         => $payment['amount'],
                        'payment_method' => $payment['method'],
                        'notes'          => $payment['reference'],
                        'payment_proof'  => $proofPath,
                    ]);
                }
            }

            if ($isBpjs) {
                $status = 'paid';

                $sale->update([
                    'paid_total' => $sale->grand_total,
                    'balance'    => 0,
                    'status'     => $status,
                ]);

            } else {

                $paidTotal = $sale->payments()->sum('amount');
                $balance   = $sale->grand_total - $paidTotal;

                $sale->update([
                    'paid_total' => $paidTotal,
                    'balance'    => $balance,
                    'status'     => $balance <= 0 ? 'paid' : ($paidTotal > 0 ? 'partial' : 'posted'),
                ]);
            }

            // Jika langsung paid
            if ($status === 'paid') {

                $this->notifyAdmins(
                    $sale,
                    "Order {$sale->sale_number} dibuat dan langsung lunas oleh " . auth()->user()->name
                );
            }

            // Jika partial saat dibuat
            if ($status === 'partial') {

                $this->notifyAdmins(
                    $sale,
                    "Order {$sale->sale_number} dibuat (Partial) oleh " . auth()->user()->name
                );
            }

            if ($status === 'posted') {

                $this->notifyAdmins(
                    $sale,
                    "Order {$sale->sale_number} dibuat (Posted) oleh " . auth()->user()->name
                );
            }

            $this->dispatch('refresh-notification');
            $this->dispatch('play-sound');
        });

        $this->dispatch('swal',
            icon: 'success',
            title: 'Success',
            text: 'Sale created successfully'
        );

        return redirect()->route('transaction.sales.index');
    }

    protected function notifyAdmins($sale, $message)
    {
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new SaleOrderNotification($sale, $message));
        }
    }

    /* =========================================
       RENDER
    ==========================================*/

    public function render()
    {
        return view('livewire.transaction.sale.create')
            ->layout('layouts.app', [
                'title' => 'Create Sale',
                'subtitle' => 'Add new sale transaction',
            ]);
    }
}