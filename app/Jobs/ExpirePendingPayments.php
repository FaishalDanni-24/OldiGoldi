<?php

namespace App\Jobs;

use App\Models\Payments\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpirePendingPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Payment::query()
            ->where('payment_status', Payment::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->each(function (Payment $payment) {
                $payment->payment_status = Payment::STATUS_EXPIRED;
                $payment->save();
            });
    }
}
