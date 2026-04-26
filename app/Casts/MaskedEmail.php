<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MaskedEmail implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (empty($value)) {
            return $value;
        }

        // Jika user yang sedang login diizinkan melihat data sensitif, kembalikan aslinya
        if (auth()->check() && \Illuminate\Support\Facades\Gate::allows('view-sensitive-data')) {
            return $value;
        }

        // Jika sistem diakses via console (misal Artisan/Tinker), tetap kembalikan asli
        if (app()->runningInConsole()) {
            return $value;
        }

        $parts = explode('@', (string) $value);
        if (count($parts) !== 2) {
            return $value;
        }

        $name = $parts[0];
        $domain = $parts[1];

        // Contoh: 'budi' -> 'bu***'
        $visibleChars = 2;
        $maskedName = substr($name, 0, $visibleChars) . str_repeat('*', max(strlen($name) - $visibleChars, 3));

        return $maskedName . '@' . $domain;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Mencegah nilai ter-masking (mengandung '*') untuk tersimpan ulang ke database
        if (is_string($value) && str_contains($value, '***')) {
            return $attributes[$key] ?? $value;
        }

        return $value;
    }
}
