<?php

namespace App\Services;

use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sandbox client for the Payment Gateway.
 * Real API calls are made against config('services.payment_gateway').
 */
class PaymentGatewayService
{
    protected function config(): array
    {
        return config('services.payment_gateway', []);
    }

    public function createTransaction(Order $order, string $provider = 'sandbox'): ?Payment
    {
        $cfg = $this->config();
        $baseUrl = $cfg['url'] ?? null;
        $apiKey = $cfg['key'] ?? null;

        $payment = $order->payment()->create([
            'provider' => $provider,
            'amount' => $order->total_amount,
            'payment_status' => Payment::STATUS_PENDING,
            'expires_at' => now()->addMinutes((int) ($cfg['expiry_minutes'] ?? 60)),
        ]);

        if ($baseUrl && $apiKey) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(15)
                    ->post(rtrim($baseUrl, '/') . '/transactions', [
                        'order_number' => $order->order_number,
                        'amount' => $order->total_amount,
                        'currency' => $cfg['currency'] ?? 'IDR',
                        'callback_url' => $cfg['callback_url'] ?? null,
                        'return_url' => $cfg['return_url'] ?? null,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $payment->provider_order_id = $data['order_id'] ?? $data['transaction_id'] ?? null;
                    $payment->payment_url = $data['payment_url'] ?? $data['redirect_url'] ?? null;
                    $payment->expires_at = isset($data['expires_at']) ? \Carbon\Carbon::parse($data['expires_at']) : $payment->expires_at;
                    $payment->save();
                } else {
                    Log::warning('PaymentGateway createTransaction failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('PaymentGateway createTransaction exception', ['error' => $e->getMessage()]);
            }
        }

        return $payment;
    }

    public function verifyCallback(array $payload, ?string $signature = null): bool
    {
        $secret = $this->config()['webhook_secret'] ?? null;
        if ($secret && $signature) {
            $expected = hash_hmac('sha256', json_encode($payload), $secret);
            if (! hash_equals($expected, $signature)) {
                return false;
            }
        }
        return true;
    }

    public function cancel(Payment $payment): bool
    {
        $payment->payment_status = Payment::STATUS_CANCELLED;
        $payment->save();
        return true;
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        $payment->payment_status = Payment::STATUS_REFUNDED;
        $payment->save();
        return true;
    }
}
