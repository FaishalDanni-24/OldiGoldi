<?php

namespace App\Jobs;

use App\Models\Payments\Payment;
use App\Models\Payments\PaymentWebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PaymentWebhookEvent $event) {}

    public function handle(): void
    {
        $payload = $this->event->payload ?? [];
        $payment = $this->event->payment;

        try {
            $status = $payload['status'] ?? $payload['payment_status'] ?? null;
            if ($payment && $status) {
                if (in_array($status, ['paid', 'success', 'settlement'], true)) {
                    $payment->markPaid($payload['transaction_id'] ?? null);
                    $payment->order?->update(['order_status' => \App\Models\Orders\Order::STATUS_PAID]);
                } elseif (in_array($status, ['failed', 'deny', 'cancel'], true)) {
                    $payment->payment_status = Payment::STATUS_FAILED;
                    $payment->save();
                } elseif ($status === 'expired') {
                    $payment->payment_status = Payment::STATUS_EXPIRED;
                    $payment->save();
                }
            }
            $this->event->markProcessed();
        } catch (\Throwable $e) {
            $this->event->markFailed($e->getMessage());
            throw $e;
        }
    }
}
