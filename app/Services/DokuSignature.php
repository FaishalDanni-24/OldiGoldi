<?php

namespace App\Services;

/**
 * DOKU non-SNAP signature helpers.
 *
 * Signature scheme (https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap):
 *   components (one per line, no trailing newline):
 *     Client-Id:{clientId}
 *     Request-Id:{requestId}
 *     Request-Timestamp:{timestamp}
 *     Request-Target:{path}
 *     Digest:{base64(sha256(body))}
 *   signature = "HMACSHA256=" + base64(hmac_sha256(components, secretKey))
 */
class DokuSignature
{
    public static function digest(string $body): string
    {
        return base64_encode(hash('sha256', $body, true));
    }

    public static function sign(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $target,
        string $body,
        string $secretKey,
    ): string {
        $digest = self::digest($body);
        $raw = "Client-Id:{$clientId}\nRequest-Id:{$requestId}\nRequest-Timestamp:{$timestamp}\nRequest-Target:{$target}\nDigest:{$digest}";

        return 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $raw, $secretKey, true));
    }

    /**
     * Verify an incoming DOKU notification signature.
     *
     * @return bool
     */
    public static function verify(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $target,
        string $body,
        string $signature,
        string $secretKey,
    ): bool {
        if ($secretKey === '' || $signature === '') {
            return false;
        }

        $expected = self::sign($clientId, $requestId, $timestamp, $target, $body, $secretKey);

        return hash_equals($expected, $signature);
    }

    /**
     * Timestamp freshness check (default ±5 minutes).
     */
    public static function isFresh(string $timestamp, int $toleranceSeconds = 300): bool
    {
        try {
            $ts = new \DateTimeImmutable($timestamp);
        } catch (\Throwable $e) {
            return false;
        }

        $now = new \DateTimeImmutable('now', $ts->getTimezone());
        $diff = abs($now->getTimestamp() - $ts->getTimestamp());

        return $diff <= $toleranceSeconds;
    }
}
