<?php

namespace App\Notifications;

use App\Models\Payments\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSucceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil')
            ->line('Pembayaran untuk pesanan #' . $this->payment->order?->order_number . ' telah berhasil.')
            ->line('Jumlah: ' . $this->payment->amount);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->payment_id,
            'order_id' => $this->payment->order_id,
            'amount' => $this->payment->amount,
        ];
    }
}
