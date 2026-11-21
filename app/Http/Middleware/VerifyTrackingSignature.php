<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTrackingSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $headerName = config('services.tracking.webhook_header', 'X-Biteship-Signature');
        $secret = config('services.tracking.webhook_secret');

        // Sandbox: no secret configured -> allow through.
        if (! $secret) {
            return $next($request);
        }

        // Biteship verification probe when adding the webhook: empty body. Accept.
        if (trim($request->getContent()) === '') {
            return $next($request);
        }

        $value = $request->header($headerName);

        if (! $value || ! hash_equals((string) $secret, (string) $value)) {
            return response()->json(['message' => 'Invalid tracking webhook signature.'], 401);
        }

        return $next($request);
    }
}
