<?php

namespace App\Traits;

trait MaskSensitiveData
{
    /**
     * Mask string dinamis
     */
    public function maskString(?string $string, int $visibleStarts = 2, int $visibleEnds = 2): ?string
    {
        if (empty($string)) {
            return $string;
        }

        // Jika user yang sedang login diizinkan melihat data sensitif, kembalikan aslinya
        if (auth()->check() && \Illuminate\Support\Facades\Gate::allows('view-sensitive-data')) {
            return $string;
        }

        $length = strlen($string);
        if ($length <= ($visibleStarts + $visibleEnds)) {
            return str_repeat('*', $length);
        }

        $masked = str_repeat('*', $length - $visibleStarts - $visibleEnds);
        return substr($string, 0, $visibleStarts) . $masked . substr($string, -$visibleEnds);
    }

    /**
     * Mask spesifik untuk format email
     */
    public function maskEmail(?string $email): ?string
    {
        if (empty($email)) {
            return $email;
        }

        if (auth()->check() && \Illuminate\Support\Facades\Gate::allows('view-sensitive-data')) {
            return $email;
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $this->maskString($email);
        }

        $name = $parts[0];
        $domain = $parts[1];

        $visibleChars = 2;
        $maskedName = substr($name, 0, $visibleChars) . str_repeat('*', max(strlen($name) - $visibleChars, 3));

        return $maskedName . '@' . $domain;
    }
}
