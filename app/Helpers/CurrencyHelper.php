<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format(int $amountCents, ?string $currency = null): string
    {
        $currency ??= config('app.currency', '$'); // fallback to $ if not set
        return $currency . number_format($amountCents / 100, 2);
    }
}
