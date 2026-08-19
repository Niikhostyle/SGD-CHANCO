<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Feriados nacionales de Chile (Ley 2.977, 19.668, 20.215, 20.299, 20.663, 21.357).
 * No incluye feriados regionales ni días de censo.
 */
class FeriadosChile
{
    public static function esHabil(Carbon $fecha): bool
    {
        $d = $fecha->copy()->startOfDay();
        if ($d->isWeekend()) {
            return false;
        }
        return !isset(self::feriadosDelAnio((int) $d->year)[$d->toDateString()]);
    }

    public static function contarHabiles(Carbon $inicio, Carbon $termino): int
    {
        $n = 0;
        $cur = $inicio->copy()->startOfDay();
        $fin = $termino->copy()->startOfDay();
        while ($cur->lte($fin)) {
            if (self::esHabil($cur)) {
                $n++;
            }
            $cur->addDay();
        }
        return $n;
    }

    /** @return array<string, string> Y-m-d => nombre */
    public static function feriadosDelAnio(int $anio): array
    {
        $out = [];
        $add = function (Carbon $f, string $nombre) use (&$out) {
            $out[$f->toDateString()] = $nombre;
        };

        $add(Carbon::create($anio, 1, 1), 'Año Nuevo');
        $pascua = self::domingoPascua($anio);
        $add($pascua->copy()->subDays(2), 'Viernes Santo');
        $add($pascua->copy()->subDay(), 'Sábado Santo');
        $add(Carbon::create($anio, 5, 1), 'Día del Trabajo');
        $add(Carbon::create($anio, 5, 21), 'Glorias Navales');
        $add(self::trasladoLunes(Carbon::create($anio, 6, 20)), 'Pueblos Indígenas');
        $add(self::trasladoLunes(Carbon::create($anio, 6, 29)), 'San Pedro y San Pablo');
        $add(self::trasladoLunes(Carbon::create($anio, 7, 16)), 'Virgen del Carmen');
        $add(self::trasladoLunes(Carbon::create($anio, 8, 15)), 'Asunción de la Virgen');
        $add(Carbon::create($anio, 9, 18), 'Independencia Nacional');
        $add(Carbon::create($anio, 9, 19), 'Glorias del Ejército');
        $dieciocho = Carbon::create($anio, 9, 18);
        if ($dieciocho->isTuesday()) {
            $add(Carbon::create($anio, 9, 17), 'Feriado 17 de septiembre');
        }
        if (Carbon::create($anio, 9, 19)->isMonday()) {
            $add(Carbon::create($anio, 9, 20), 'Feriado 20 de septiembre');
        }
        $add(self::trasladoLunes(Carbon::create($anio, 10, 12)), 'Encuentro de Dos Mundos');
        $add(self::trasladoLunes(Carbon::create($anio, 10, 31)), 'Iglesias Evangélicas');
        $add(self::trasladoLunes(Carbon::create($anio, 11, 1)), 'Todos los Santos');
        $add(Carbon::create($anio, 12, 8), 'Inmaculada Concepción');
        $add(Carbon::create($anio, 12, 25), 'Navidad');

        return $out;
    }

    /** Ley 19.668: martes → lunes anterior; mié/jue/vie → lunes siguiente. */
    private static function trasladoLunes(Carbon $fecha): Carbon
    {
        $d = $fecha->copy()->startOfDay();
        if ($d->isTuesday()) {
            return $d->subDay();
        }
        if ($d->isWednesday() || $d->isThursday() || $d->isFriday()) {
            return $d->next(Carbon::MONDAY);
        }
        return $d;
    }

    private static function domingoPascua(int $anio): Carbon
    {
        $a = $anio % 19;
        $b = intdiv($anio, 100);
        $c = $anio % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;
        return Carbon::create($anio, $mes, $dia);
    }
}
