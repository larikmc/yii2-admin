<?php

namespace larikmc\admin\services;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class TotpService extends Component
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $length = 32): string
    {
        $secret = '';
        $alphabet = self::BASE32_ALPHABET;
        $maxIndex = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, $maxIndex)];
        }

        return $secret;
    }

    public function getOtpAuthUri(string $secret, string $accountName, string $issuer): string
    {
        $encodedIssuer = rawurlencode($issuer);
        $encodedAccount = rawurlencode($accountName);

        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            $encodedIssuer,
            $encodedAccount,
            $secret,
            $encodedIssuer
        );
    }

    public function generateQrCodeSvg(string $otpAuthUri, int $size = 200): string
    {
        if (class_exists(\BaconQrCode\Renderer\ImageRenderer::class)) {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $svg = $writer->writeString($otpAuthUri);

            // Strip XML declaration if present so it embeds cleanly in HTML
            return preg_replace('/<\?xml.*?\?>/i', '', $svg) ?? $svg;
        }

        // Clean standalone fallback inline SVG generator (pure PHP matrix)
        return $this->renderFallbackSvg($otpAuthUri, $size);
    }

    public function verifyCode(string $secret, string $code, int $discrepancy = 1, ?int $timestamp = null): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $timestamp = $timestamp ?? time();
        $currentSlice = (int) floor($timestamp / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->calculateCode($secret, $currentSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    public function calculateCode(string $secret, int $slice): string
    {
        $secretBinary = $this->base32Decode($secret);
        if ($secretBinary === '') {
            return '';
        }

        // Pack slice into 8-byte big-endian binary string
        $binaryTime = pack('N*', 0) . pack('N*', $slice);
        $hash = hash_hmac('sha1', $binaryTime, $secretBinary, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $truncatedHash = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $pinValue = $truncatedHash % 1000000;

        return str_pad((string) $pinValue, 6, '0', STR_PAD_LEFT);
    }

    public function base32Decode(string $b32): string
    {
        $b32 = strtoupper(trim($b32));
        $b32 = rtrim($b32, '=');
        if ($b32 === '') {
            return '';
        }

        $alphabet = self::BASE32_ALPHABET;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                return '';
            }
            $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $bytes = [];
        $length = strlen($binary);
        for ($i = 0; $i + 8 <= $length; $i += 8) {
            $bytes[] = chr(bindec(substr($binary, $i, 8)));
        }

        return implode('', $bytes);
    }

    private function renderFallbackSvg(string $text, int $size): string
    {
        // Simple SVG fallback placeholder if BaconQrCode is not installed
        $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">' .
            '<rect width="100%" height="100%" fill="#f1f5f9"/>' .
            '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="12" fill="#64748b">' .
            'QR Code Library required' .
            '</text></svg>';
    }
}
