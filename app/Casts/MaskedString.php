<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MaskedString implements CastsAttributes
{
    /**
     * Pilihan panjang karakter yang ditampilkan
     */
    protected int $visibleStarts;
    protected int $visibleEnds;

    public function __construct(int $visibleStarts = 2, int $visibleEnds = 2)
    {
        $this->visibleStarts = $visibleStarts;
        $this->visibleEnds = $visibleEnds;
    }

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

        $string = (string) $value;
        $length = strlen($string);

        if ($length <= ($this->visibleStarts + $this->visibleEnds)) {
            return str_repeat('*', $length);
        }

        $masked = str_repeat('*', $length - $this->visibleStarts - $this->visibleEnds);
        return substr($string, 0, $this->visibleStarts) . $masked . substr($string, -$this->visibleEnds);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Mencegah nilai ter-masking untuk tersimpan ulang ke database
        if (is_string($value) && str_contains($value, '***')) {
            return $attributes[$key] ?? $value;
        }

        return $value;
    }
}
