<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SaleOrderNotification extends Notification
{
    use Queueable;

    protected $sale;
    protected $message;

    public function __construct($sale, $message)
    {
        $this->sale = $sale;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'sale_id' => $this->sale->id,
            'sale_number' => $this->sale->sale_number,
            'message' => $this->message,
            'url' => route('transaction.sales.show', $this->sale->id),
        ];
    }
}