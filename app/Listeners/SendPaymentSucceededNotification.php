<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Notifications\PaymentSucceededNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentSucceededNotification implements ShouldQueue
{
    public function handle(PaymentSucceeded $event): void
    {
        $user = $event->payment->order?->customer;
        if ($user) {
            $user->notify(new PaymentSucceededNotification($event->payment));
        }
    }
}
