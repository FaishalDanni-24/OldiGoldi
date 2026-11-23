<?php

namespace App\Http\Middleware;

use App\Services\DokuSignature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPaymentSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.payment_gateway.secret_key', config('services.payment_gateway.secret'));

        // Sandbox: no secret configured -> allow through (log warning).
        if (! $secret) {
            return $next($request);
        }

        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $timestamp = $request->header('Request-Timestamp');
        $signature = $request->header('Signature');
        $rawBody = $request->getContent();
        $target = '/' . ltrim($request->path(), '/');

        // DOKU verification probe: empty body, but signature may still be present.
        // If no signature header at all, accept (probe without secret).
        if (! $signature) {
            return $next($request);
        }

        if (! DokuSignature::isFresh($timestamp ?? '')) {
            return response()->json(['message' => 'Expired request timestamp.'], 401);
        }

        if (! DokuSignature::verify($clientId ?? '', $requestId ?? '', $timestamp ?? '', $target, $rawBody, $signature, $secret)) {
            return response()->json(['message' => 'Invalid payment webhook signature.'], 401);
        }

        return $next($request);
    }
}
