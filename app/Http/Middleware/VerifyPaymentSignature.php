<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPaymentSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.payment_gateway.webhook_secret');

        if (! $secret) {
            // Sandbox: no secret configured, allow through.
            return $next($request);
        }

        $signature = $request->header('X-Signature')
            ?? $request->header('X-Webhook-Signature')
            ?? $request->header('X-Payment-Signature');

        $raw = $request->getContent();
        $expected = hash_hmac('sha256', $raw, $secret);

        if (! $signature || ! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid payment webhook signature.'], 401);
        }

        return $next($request);
    }
}
