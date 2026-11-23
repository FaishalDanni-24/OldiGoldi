<?php

namespace App\Services;

use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * DOKU Checkout (Non-SNAP) payment gateway client.
 *
 * Docs: https://developers.doku.com/accept-payments/doku-checkout
 */
class PaymentGatewayService
{
    protected function config(): array
    {
        return config('services.payment_gateway', []);
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config()['base_url'] ?? $this->config()['url'] ?? 'https://api-sandbox.doku.com', '/');
    }

    protected function clientId(): ?string
    {
        return $this->config()['client_id'] ?? $this->config()['key'] ?? null;
    }

    protected function secretKey(): ?string
    {
        return $this->config()['secret_key'] ?? $this->config()['secret'] ?? null;
    }

    public function createTransaction(Order $order, string $provider = 'doku'): ?Payment
    {
        $cfg = $this->config();
        $clientId = $this->clientId();
        $secretKey = $this->secretKey();
        $baseUrl = $this->baseUrl();

        $payment = $order->payment()->create([
            'provider' => $provider,
            'amount' => $order->total_amount,
            'payment_status' => Payment::STATUS_PENDING,
            'expires_at' => now()->addMinutes((int) ($cfg['expiry_minutes'] ?? 60)),
        ]);

        if (! $clientId || ! $secretKey) {
            // Sandbox fallback: no credentials configured, keep pending payment.
            Log::info('DOKU credentials not configured; payment kept pending.', ['order_id' => $order->order_id]);
            return $payment;
        }

        $order->loadMissing('items.product', 'customer', 'address');

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'id' => (string) $item->product_id,
                'name' => $item->product_name_snapshot,
                'price' => (int) round((float) $item->unit_price),
                'quantity' => (int) $item->quantity,
                'sku' => $item->sku_snapshot,
            ];
        }

        $customer = $order->customer;
        $address = $order->address;

        $body = [
            'order' => [
                'amount' => (int) round((float) $order->total_amount),
                'invoice_number' => $order->order_number,
                'currency' => $cfg['currency'] ?? 'IDR',
                'callback_url' => $cfg['return_url'] ?? $cfg['callback_url'] ?? null,
                'callback_url_result' => $cfg['return_url'] ?? $cfg['callback_url'] ?? null,
                'language' => 'EN',
                'auto_redirect' => true,
                'line_items' => $lineItems,
            ],
            'payment' => [
                'payment_due_date' => (int) ($cfg['expiry_minutes'] ?? 60),
            ],
            'customer' => [
                'id' => (string) $customer?->user_id,
                'name' => $customer?->name,
                'email' => $customer?->email,
                'phone' => $customer?->phone_number,
                'address' => $address?->address_line,
                'city' => $address?->city,
                'postcode' => $address?->postal_code,
                'state' => $address?->province,
                'country' => 'ID',
            ],
        ];

        if ($cfg['notification_url'] ?? null) {
            $body['additional_info'] = [
                'override_notification_url' => $cfg['notification_url'],
            ];
        }

        $path = '/checkout/v1/payment';
        $requestId = (string) Str::uuid();
        $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $jsonBody = json_encode($body);
        $signature = DokuSignature::sign($clientId, $requestId, $timestamp, $path, $jsonBody, $secretKey);

        try {
            $response = Http::withHeaders([
                'Client-Id' => $clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($baseUrl . $path, $body);

            if ($response->successful()) {
                $data = $response->json('response', $response->json());
                $payment->provider = 'doku';
                $payment->provider_order_id = $order->order_number; // invoice_number for webhook match
                $payment->provider_transaction_id = $data['payment']['token_id'] ?? null;
                $payment->payment_url = $data['payment']['url'] ?? null;
                $payment->expires_at = isset($data['payment']['expired_date'])
                    ? \Carbon\Carbon::createFromFormat('YmdHis', $data['payment']['expired_date'], 'Asia/Jakarta')->utc()
                    : $payment->expires_at;
                $payment->save();
            } else {
                Log::warning('DOKU createTransaction failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \App\Exceptions\ExternalServiceException(
                    'DOKU',
                    $response->status(),
                    'DOKU createTransaction failed: HTTP ' . $response->status()
                );
            }
        } catch (\Throwable $e) {
            Log::error('DOKU createTransaction exception', ['error' => $e->getMessage()]);
            if (! $e instanceof \App\Exceptions\ExternalServiceException) {
                throw new \App\Exceptions\ExternalServiceException('DOKU', null, $e->getMessage(), $e);
            }
            throw $e;
        }

        return $payment;
    }

    public function verifyCallback(array $payload, ?string $signature = null, ?string $clientId = null, ?string $requestId = null, ?string $timestamp = null, string $target = '/api/v1/payments/webhook/doku'): bool
    {
        $secret = $this->secretKey();
        if (! $secret) {
            return true; // sandbox: no secret configured
        }
        if (! $clientId || ! $requestId || ! $timestamp || ! $signature) {
            return false;
        }
        $body = json_encode($payload);

        return DokuSignature::verify($clientId, $requestId, $timestamp, $target, $body, $signature, $secret);
    }

    public function verifyCallbackRaw(string $rawBody, ?string $signature, ?string $clientId, ?string $requestId, ?string $timestamp, string $target): bool
    {
        $secret = $this->secretKey();
        if (! $secret) {
            return true;
        }
        if (! $clientId || ! $requestId || ! $timestamp || ! $signature) {
            return false;
        }

        return DokuSignature::verify($clientId, $requestId, $timestamp, $target, $rawBody, $signature, $secret);
    }

    public function checkStatus(string $invoiceNumber): ?array
    {
        $clientId = $this->clientId();
        $secretKey = $this->secretKey();
        if (! $clientId || ! $secretKey) {
            return null;
        }

        $path = '/orders/v1/status/' . $invoiceNumber;
        $requestId = (string) Str::uuid();
        $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $body = '';
        $signature = DokuSignature::sign($clientId, $requestId, $timestamp, $path, $body, $secretKey);

        try {
            $response = Http::withHeaders([
                'Client-Id' => $clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
            ])->timeout(15)->get($this->baseUrl() . $path);

            if ($response->successful()) {
                return $response->json();
            }
            Log::warning('DOKU checkStatus failed', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('DOKU checkStatus exception', ['error' => $e->getMessage()]);
        }

        return null;
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
