<?php
namespace App\Libraries;

class Jwt
{
    private const ALGOS = [
        'HS256' => 'sha256',
    ];

    public static function encode(array $payload, string $secret, string $alg = 'HS256'): string
    {
        if (!isset(self::ALGOS[$alg])) {
            throw new \InvalidArgumentException('Algoritmo JWT não suportado');
        }
        $header = ['typ' => 'JWT', 'alg' => $alg];
        $segments = [
            self::base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)),
            self::base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR)),
        ];
        $signature = hash_hmac(self::ALGOS[$alg], implode('.', $segments), $secret, true);
        $segments[] = self::base64UrlEncode($signature);
        return implode('.', $segments);
    }

    public static function decode(string $token, string $secret, string $alg = 'HS256'): ?array
    {
        if (!isset(self::ALGOS[$alg])) {
            return null;
        }
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$header64, $payload64, $signature64] = $parts;
        $header = json_decode(self::base64UrlDecode($header64), true);
        if (!is_array($header) || ($header['alg'] ?? '') !== $alg) {
            return null;
        }
        $payloadJson = self::base64UrlDecode($payload64);
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }
        $signature = self::base64UrlDecode($signature64);
        $expected = hash_hmac(self::ALGOS[$alg], $header64 . '.' . $payload64, $secret, true);
        if (!hash_equals($expected, $signature)) {
            return null;
        }
        if (isset($payload['exp']) && time() >= (int)$payload['exp']) {
            return null;
        }
        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
