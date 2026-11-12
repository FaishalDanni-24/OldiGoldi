<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTrackingSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.tracking.webhook_secret');

        if (! $secret) {
            return $next($request);
        }

        $signature = $request->header('X-Signature')
            ?? $request->header('X-Webhook-Signature')
            ?? $request->header('X-Tracking-Signature');

        $raw = $request->getContent();
        $expected = hash_hmac('sha256', $raw, $secret);

        if (! $signature || ! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid tracking webhook signature.'], 401);
        }

        return $next($request);
    }
}
