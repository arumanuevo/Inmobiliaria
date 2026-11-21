<?php

namespace App\Support;

/**
 * Formato de dinero en español (Argentina):
 * miles con punto, decimales con coma  ->  $85.000.000,00
 */
class Dinero
{
    public static function fmt($amount, int $decimals = 2): string
    {
        return '$' . number_format((float) $amount, $decimals, ',', '.');
    }

    // Sin símbolo, por si hace falta (feeds, totales, etc.)
    public static function num($amount, int $decimals = 2): string
    {
        return number_format((float) $amount, $decimals, ',', '.');
    }
}
