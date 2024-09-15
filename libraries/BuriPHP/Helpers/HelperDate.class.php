<?php

namespace Libraries\BuriPHP\Helpers;

use BuriPHP\Settings;
use IntlDateFormatter;

/**
 * Clase abstracta HelperDate
 * 
 * Esta clase proporciona métodos auxiliares relacionados con la manipulación y 
 * el manejo de fechas.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 2.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperDate
{
    /**
     * Obtiene la fecha actual.
     *
     * @param bool $withTime Indica si se debe incluir la hora en la fecha. 
     *                       Si es true, se devuelve la fecha y la hora en el formato 'Y-m-d H:i:s'.
     *                       Si es false, se devuelve solo la fecha en el formato 'Y-m-d'.
     * @return string La fecha actual, con o sin hora según el parámetro $withTime.
     */
    public static function getCurrentDate($withTime = false)
    {
        return $withTime ? date('Y-m-d H:i:s') : date('Y-m-d');
    }

    /**
     * Obtiene la fecha actual en GMT.
     *
     * @param bool $withTime Indica si se debe incluir la hora en el formato de la fecha.
     *                       - true: Devuelve la fecha y la hora en formato 'Y-m-d H:i:s'.
     *                       - false: Devuelve solo la fecha en formato 'Y-m-d'.
     * @return string La fecha actual en GMT, con o sin hora según el parámetro $withTime.
     */
    public static function getCurrentDateGMT($withTime = false)
    {
        return $withTime ? gmdate('Y-m-d H:i:s') : gmdate('Y-m-d');
    }

    /**
     * Obtiene la fecha actual con la zona horaria especificada.
     *
     * @param string $timezone La zona horaria a utilizar.
     * @param bool $withTime Si se debe incluir la hora en el formato de la fecha. Por defecto es false.
     * @return string La fecha actual en el formato 'Y-m-d' o 'Y-m-d H:i:s' dependiendo del valor de $withTime.
     */
    public static function getCurrentDateWithTimezone($timezone, $withTime = false)
    {
        $dateTime = new \DateTime('now', new \DateTimeZone($timezone));
        return $withTime ? $dateTime->format('Y-m-d H:i:s') : $dateTime->format('Y-m-d');
    }

    /**
     * Obtiene el día actual del mes.
     *
     * @return int El día actual del mes como un entero.
     */
    public static function getCurrentDay()
    {
        return (int)date('d');
    }

    /**
     * Obtiene el mes actual.
     *
     * @return int El mes actual como un número entero (1-12).
     */
    public static function getCurrentMonth()
    {
        return (int)date('m');
    }

    /**
     * Obtiene el año actual.
     *
     * @return int El año actual.
     */
    public static function getCurrentYear()
    {
        return (int)date('Y');
    }

    /**
     * Añade un número específico de minutos a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d H:i:s'.
     * @param int $minutes El número de minutos a añadir a la fecha.
     * @return string La nueva fecha con los minutos añadidos en formato 'Y-m-d H:i:s'.
     */
    public static function addMinutesToDate($date, $minutes)
    {
        $dateTime = new \DateTime($date);
        $dateTime->modify("+{$minutes} minutes");
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Añade un número específico de días a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d'.
     * @param int $days El número de días a añadir a la fecha.
     * @return string La nueva fecha con los días añadidos en formato 'Y-m-d'.
     */
    public static function addDaysToDate($date, $days)
    {
        $dateTime = new \DateTime($date);
        $dateTime->modify("+{$days} days");
        return $dateTime->format('Y-m-d');
    }

    /**
     * Resta una cantidad específica de días a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d'.
     * @param int $days La cantidad de días a restar de la fecha.
     * @return string La fecha resultante después de restar los días, en formato 'Y-m-d'.
     */
    public static function subtractDaysFromDate($date, $days)
    {
        $dateTime = new \DateTime($date);
        $dateTime->modify("-{$days} days");
        return $dateTime->format('Y-m-d');
    }

    /**
     * Obtiene el texto del día de la semana para una fecha dada.
     *
     * @param string $date La fecha en formato de cadena.
     * @param string $locale El locale para la formateación de la fecha (por defecto es 'es_ES').
     * @return string El nombre del día de la semana con la primera letra en mayúscula.
     */
    public static function getDayOfWeekText($date, $locale = 'es_ES')
    {
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $formatter->setPattern('EEEE');
        return ucfirst($formatter->format(strtotime($date)));
    }

    /**
     * Obtiene el número del día de la semana para una fecha dada.
     *
     * @param string $date La fecha en formato de cadena.
     * @return int El número del día de la semana (0 para domingo, 1 para lunes, ..., 6 para sábado).
     */
    public static function getDayOfWeekNumber($date)
    {
        return (int)date('w', strtotime($date));
    }

    /**
     * Obtiene el primer y último día del mes a partir de una fecha dada.
     *
     * @param string $date La fecha de la cual se obtendrá el primer y último día del mes.
     * @return array Un arreglo que contiene el primer día del mes en la posición 0 y el último día del mes en la posición 1.
     */
    public static function getFirstAndLastDayOfMonthFromDate($date)
    {
        return [
            Self::getFirstDayOfMonth(Self::getMonthFromDate($date), Self::getYearFromDate($date)),
            Self::getLastDayOfMonth(Self::getMonthFromDate($date), Self::getYearFromDate($date))
        ];
    }

    /**
     * Obtiene el primer día del mes especificado en formato 'YYYY-MM-DD'.
     *
     * @param int $month El mes para el cual se desea obtener el primer día (1-12).
     * @param int $year El año para el cual se desea obtener el primer día.
     * @return string La fecha del primer día del mes en formato 'YYYY-MM-DD'.
     */
    public static function getFirstDayOfMonth($month, $year)
    {
        return sprintf('%04d-%02d-01', $year, $month);
    }

    /**
     * Obtiene el último día del mes para un mes y año específicos.
     *
     * @param int $month El mes en formato numérico (1-12).
     * @param int $year El año en formato numérico (e.g., 2023).
     * @return string La fecha del último día del mes en formato "Y-m-t".
     */
    public static function getLastDayOfMonth($month, $year)
    {
        return date("Y-m-t", strtotime("$year-$month-01"));
    }

    /**
     * Obtiene el primer día hábil del mes especificado.
     *
     * @param int $month El mes para el cual se desea obtener el primer día hábil.
     * @param int $year El año para el cual se desea obtener el primer día hábil.
     * @return string La fecha del primer día hábil del mes en formato 'Y-m-d'.
     */
    public static function getFirstBusinessDayOfMonth($month, $year)
    {
        $firstDay = self::getFirstDayOfMonth($month, $year);
        $dayOfWeek = date('N', strtotime($firstDay)); // 1 (for Monday) through 7 (for Sunday)

        if ($dayOfWeek == 6) { // Saturday
            $firstDay = date('Y-m-d', strtotime($firstDay . ' +2 days'));
        } elseif ($dayOfWeek == 7) { // Sunday
            $firstDay = date('Y-m-d', strtotime($firstDay . ' +1 day'));
        }

        return $firstDay;
    }

    /**
     * Obtiene el último día hábil del mes especificado.
     *
     * @param int $month El mes para el cual se desea obtener el último día hábil.
     * @param int $year El año para el cual se desea obtener el último día hábil.
     * @return string El último día hábil del mes en formato 'Y-m-d'.
     */
    public static function getLastBusinessDayOfMonth($month, $year)
    {
        $lastDay = self::getLastDayOfMonth($month, $year);
        $dayOfWeek = date('N', strtotime($lastDay)); // 1 (for Monday) through 7 (for Sunday)

        if ($dayOfWeek == 6) { // Saturday
            $lastDay = date('Y-m-d', strtotime($lastDay . ' -1 day'));
        } elseif ($dayOfWeek == 7) { // Sunday
            $lastDay = date('Y-m-d', strtotime($lastDay . ' -2 days'));
        }

        return $lastDay;
    }

    /**
     * Calcula el número de días hábiles entre dos fechas, excluyendo fines de semana y días festivos.
     *
     * @param string $startDate Fecha de inicio en formato 'Y-m-d'.
     * @param string $endDate Fecha de fin en formato 'Y-m-d'.
     * @param array $holidays (Opcional) Array de fechas festivas en formato 'Y-m-d' que deben ser excluidas.
     * @return int Número de días hábiles entre las dos fechas.
     */
    public static function getBusinessDays($startDate, $endDate, $holidays = [])
    {
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        $businessDays = 0;

        while ($startDate <= $endDate) {
            if ($startDate->format('N') < 6 && !in_array($startDate->format('Y-m-d'), $holidays)) { // 1 (for Monday) through 5 (for Friday)
                $businessDays++;
            }
            $startDate->modify('+1 day');
        }

        return $businessDays;
    }

    /**
     * Obtiene el número de días en un mes específico de un año dado.
     *
     * @param int $month El mes para el cual se desea obtener el número de días (1-12).
     * @param int $year El año para el cual se desea obtener el número de días.
     * @return int El número de días en el mes especificado.
     */
    public static function getDaysInMonth($month, $year)
    {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * Verifica si una fecha está entre dos fechas dadas.
     *
     * @param string $date La fecha a verificar.
     * @param string $start La fecha de inicio del rango.
     * @param string $end La fecha de fin del rango.
     * @param bool $closed Indica si el rango es cerrado (incluye las fechas de inicio y fin). Por defecto es true.
     * @return bool Devuelve true si la fecha está entre las fechas de inicio y fin, false en caso contrario.
     */
    public static function betweenDates($date, $start, $end, $closed = true): bool
    {
        $dateTimestamp = strtotime($date);
        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime($end);

        if ($closed) {
            return $dateTimestamp >= $startTimestamp && $dateTimestamp <= $endTimestamp;
        } else {
            return $dateTimestamp > $startTimestamp && $dateTimestamp < $endTimestamp;
        }
    }

    /**
     * Calcula el número de días entre dos fechas.
     *
     * @param string $startDate La fecha de inicio en formato 'Y-m-d'.
     * @param string $endDate La fecha de fin en formato 'Y-m-d'.
     * @return int El número de días entre las dos fechas.
     */
    public static function getDaysBetweenDates($startDate, $endDate)
    {
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        $interval = $startDate->diff($endDate);
        return $interval->days;
    }

    /**
     * Calcula el número de días entre dos fechas sin contar los fines de semana.
     *
     * @param string $startDate La fecha de inicio en formato 'Y-m-d'.
     * @param string $endDate La fecha de fin en formato 'Y-m-d'.
     * @return int El número de días entre las dos fechas excluyendo los fines de semana.
     */
    public static function getDaysBetweenDatesWithoutWeekends($startDate, $endDate)
    {
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        $interval = $startDate->diff($endDate);
        $days = $interval->days;
        $weekends = 0;

        while ($startDate <= $endDate) {
            if ($startDate->format('N') > 5) { // 6 (for Saturday) and 7 (for Sunday)
                $weekends++;
            }
            $startDate->modify('+1 day');
        }

        return $days - $weekends;
    }

    /**
     * Calcula el número de semanas entre dos fechas.
     *
     * @param string $startDate La fecha de inicio en formato 'Y-m-d'.
     * @param string $endDate La fecha de fin en formato 'Y-m-d'.
     * @return int El número de semanas completas entre las dos fechas.
     */
    public static function getWeeksBetweenDates($startDate, $endDate)
    {
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        $interval = $startDate->diff($endDate);
        return floor($interval->days / 7);
    }

    /**
     * Obtiene el número de semana a partir de una fecha dada.
     *
     * @param string $date La fecha en formato de cadena.
     * @return int El número de semana correspondiente a la fecha proporcionada.
     */
    public static function getWeekNumberFromDate($date)
    {
        $date = new \DateTime($date);
        return (int)$date->format("W");
    }

    /**
     * Verifica si una fecha dada cae en fin de semana.
     *
     * @param string $date La fecha en formato 'Y-m-d'.
     * @return bool Devuelve true si la fecha cae en sábado o domingo, de lo contrario false.
     */
    public static function isWeekend($date)
    {
        $dayOfWeek = date('N', strtotime($date)); // 1 (for Monday) through 7 (for Sunday)
        return $dayOfWeek >= 6; // 6 (for Saturday) and 7 (for Sunday)
    }

    /**
     * Obtiene el mes de una fecha dada en formato 'YYYY-MM-DD'.
     *
     * @param string $date La fecha en formato 'YYYY-MM-DD'.
     * @return int El mes extraído de la fecha como un entero.
     */
    public static function getMonthFromDate($date)
    {
        $dateParts = explode('-', $date);
        return intval($dateParts[1]);
    }

    /**
     * Obtiene el nombre del mes a partir de una fecha dada.
     *
     * @param string $date La fecha en formato de cadena.
     * @param string $locale El locale para la formateación de la fecha (por defecto 'es_ES').
     * @return string El nombre del mes con la primera letra en mayúscula.
     */
    public static function getMonthNameFromDate($date, $locale = 'es_ES')
    {
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $formatter->setPattern('MMMM');
        return ucfirst($formatter->format(strtotime($date)));
    }

    /**
     * Añade una cantidad específica de meses a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d'.
     * @param int $months La cantidad de meses a añadir a la fecha.
     * @return string La nueva fecha con los meses añadidos en formato 'Y-m-d'.
     */
    public static function addMonthsToDate($date, $months)
    {
        $date = new \DateTime($date);
        $date->modify("+{$months} months");
        return $date->format('Y-m-d');
    }

    /**
     * Resta una cantidad específica de meses a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d'.
     * @param int $months La cantidad de meses a restar.
     * @return string La fecha resultante en formato 'Y-m-d'.
     */
    public static function subtractMonthsFromDate($date, $months)
    {
        $date = new \DateTime($date);
        $date->modify("-{$months} months");
        return $date->format('Y-m-d');
    }

    /**
     * Obtiene el año de una fecha dada en formato 'YYYY-MM-DD'.
     *
     * @param string $date La fecha en formato 'YYYY-MM-DD'.
     * @return int El año extraído de la fecha.
     */
    public static function getYearFromDate($date)
    {
        $dateParts = explode('-', $date);
        return intval($dateParts[0]);
    }

    /**
     * Verifica si un año es bisiesto.
     *
     * Un año es bisiesto si es divisible por 4, pero no por 100, 
     * a menos que también sea divisible por 400.
     *
     * @param int $year El año a verificar.
     * @return bool Verdadero si el año es bisiesto, falso en caso contrario.
     */
    public static function isLeapYear($year)
    {
        return ((($year % 4) == 0) && (($year % 100) != 0)) || (($year % 400) == 0);
    }

    /**
     * Obtiene la fecha completa formateada según el formato y la configuración regional especificados.
     *
     * @param string $date La fecha a formatear.
     * @param string $dateFormatter El formato de la fecha. Puede ser 'FULL', 'LONG' o 'MEDIUM'. Por defecto es 'FULL'.
     * @param string $locale La configuración regional a utilizar para el formateo. Por defecto es 'es_ES'.
     * @return string La fecha formateada según el formato y la configuración regional especificados.
     */
    public static function getFullDate($date, $dateFormatter = 'FULL', $locale = 'es_ES')
    {
        switch ($dateFormatter) {
            default:
            case 'FULL':
                $dateFormatter = IntlDateFormatter::FULL;
                break;

            case 'LONG':
                $dateFormatter = IntlDateFormatter::LONG;
                break;

            case 'MEDIUM':
                $dateFormatter = IntlDateFormatter::MEDIUM;
                break;
        }

        $formatter = new IntlDateFormatter($locale, $dateFormatter, IntlDateFormatter::NONE);

        return $formatter->format(strtotime($date));
    }

    /**
     * Obtiene el día, mes y año de una fecha dada en formato 'YYYY-MM-DD'.
     *
     * @param string $date La fecha en formato 'YYYY-MM-DD'.
     * @return array Un arreglo asociativo con las claves 'day', 'month' y 'year'.
     */
    public static function getDayMonthYearFromDate($date)
    {
        $dateParts = explode('-', $date);
        return [
            'day' => intval($dateParts[2]),
            'month' => intval($dateParts[1]),
            'year' => intval($dateParts[0])
        ];
    }

    /**
     * Añade un intervalo de tiempo a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d H:i:s'.
     * @param int $days Número de días a añadir. Por defecto es 0.
     * @param int $months Número de meses a añadir. Por defecto es 0.
     * @param int $years Número de años a añadir. Por defecto es 0.
     * @param int $hours Número de horas a añadir. Por defecto es 0.
     * @param int $minutes Número de minutos a añadir. Por defecto es 0.
     * @param int $seconds Número de segundos a añadir. Por defecto es 0.
     * @return string La nueva fecha con el intervalo añadido en formato 'Y-m-d H:i:s'.
     */
    public static function addToDate($date, $days = 0, $months = 0, $years = 0, $hours = 0, $minutes = 0, $seconds = 0)
    {
        $dateTime = new \DateTime($date);
        $dateInterval = new \DateInterval("P{$years}Y{$months}M{$days}DT{$hours}H{$minutes}M{$seconds}S");
        $dateTime->add($dateInterval);
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Resta una cantidad específica de tiempo a una fecha dada.
     *
     * @param string $date La fecha inicial en formato 'Y-m-d H:i:s'.
     * @param int $days Número de días a restar. (Opcional, por defecto 0)
     * @param int $months Número de meses a restar. (Opcional, por defecto 0)
     * @param int $years Número de años a restar. (Opcional, por defecto 0)
     * @param int $hours Número de horas a restar. (Opcional, por defecto 0)
     * @param int $minutes Número de minutos a restar. (Opcional, por defecto 0)
     * @param int $seconds Número de segundos a restar. (Opcional, por defecto 0)
     * @return string La fecha resultante en formato 'Y-m-d H:i:s'.
     */
    public static function subtractFromDate($date, $days = 0, $months = 0, $years = 0, $hours = 0, $minutes = 0, $seconds = 0)
    {
        $dateTime = new \DateTime($date);
        $dateInterval = new \DateInterval("P{$years}Y{$months}M{$days}DT{$hours}H{$minutes}M{$seconds}S");
        $dateTime->sub($dateInterval);
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Compara dos fechas y determina su orden cronológico.
     *
     * @param string $date1 La primera fecha en formato de cadena.
     * @param string $date2 La segunda fecha en formato de cadena.
     * @return int Retorna -1 si $date1 es anterior a $date2, 1 si $date1 es posterior a $date2, y 0 si ambas fechas son iguales.
     */
    public static function compareDates($date1, $date2)
    {
        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);

        if ($timestamp1 < $timestamp2) {
            return -1;
        } elseif ($timestamp1 > $timestamp2) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Obtiene el separador de fecha de una cadena de fecha dada.
     *
     * Esta función analiza una cadena de fecha y devuelve el separador utilizado en la fecha.
     * Puede ser '/', '-' o '.'.
     *
     * @param string $date La cadena de fecha a analizar.
     * @return string El separador de fecha encontrado ('/', '-', '.'), o una cadena vacía si no se encuentra ningún separador.
     */
    public static function getDateSeparator($date)
    {
        if (strpos($date, '/') !== false) {
            return '/';
        } elseif (strpos($date, '-') !== false) {
            return '-';
        } elseif (strpos($date, '.') !== false) {
            return '.';
        } else {
            return '';
        }
    }

    /**
     * Obtiene el patrón de expresión regular correspondiente a un formato de fecha dado.
     *
     * @param string $format El formato de fecha para el cual se desea obtener el patrón. 
     *                       Los formatos soportados son:
     *                       - 'dd/mm/yyyy'
     *                       - 'mm/dd/yyyy'
     *                       - 'yyyy/mm/dd'
     *                       - 'dd-mm-yyyy'
     *                       - 'mm-dd-yyyy'
     *                       - 'yyyy-mm-dd'
     *                       - 'dd.mm.yyyy'
     *                       - 'mm.dd.yyyy'
     *                       - 'yyyy.mm.dd'
     * 
     * @return string El patrón de expresión regular correspondiente al formato de fecha dado.
     *                Si el formato no es soportado, se devuelve una cadena vacía.
     */
    public static function getPatternFromDate($format = 'yyyy-mm-dd')
    {
        switch ($format) {
            case 'dd/mm/yyyy':
            case 'mm/dd/yyyy':
                return "\\d{2}/\\d{2}/\\d{4}";

            case 'yyyy/mm/dd':
                return "\\d{4}/\\d{2}/\\d{2}";

            case 'dd-mm-yyyy':
            case 'mm-dd-yyyy':
                return "\\d{2}\\-\\d{2}\\-\\d{4}";

            case 'yyyy-mm-dd':
                return "\\d{4}\\-\\d{2}\\-\\d{2}";

            case 'dd.mm.yyyy':
            case 'mm.dd.yyyy':
                return "\\d{2}\\.\\d{2}\\.\\d{4}";

            case 'yyyy.mm.dd':
                return "\\d{4}\\.\\d{2}\\.\\d{2}";

            default:
                return "";
                break;
        }
    }

    /**
     * Calcula la edad a partir de una fecha de nacimiento.
     *
     * @param string $birthdate La fecha de nacimiento en formato 'YYYY-MM-DD'.
     * @return int La edad calculada en años.
     */
    public static function calculateAgeFromBirthdate($birthdate)
    {
        $birthDate = new \DateTime($birthdate);
        $currentDate = new \DateTime();
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }

    /**
     * Convierte una fecha de un formato a otro.
     *
     * @param string $date La fecha a convertir.
     * @param string $fromFormat El formato actual de la fecha.
     * @param string $toFormat El formato al que se desea convertir la fecha.
     * @return string La fecha convertida al nuevo formato.
     */
    public static function convertDateFormat($date, $fromFormat, $toFormat)
    {
        $dateTime = \DateTime::createFromFormat($fromFormat, $date);
        if ($dateTime === false) {
            throw new \Exception("Invalid date or format: $date, $fromFormat");
        }
        return $dateTime->format($toFormat);
    }

    /**
     * Detecta el formato de una fecha dada.
     *
     * @param string $date La fecha a analizar.
     * @return string El formato de la fecha detectada.
     */
    public static function detectDateTimeFormatWithTimezone($date)
    {
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
            'd-m-Y',
            'm-d-Y',
            'Y.m.d',
            'd.m.Y',
            'm.d.Y',
            'Y-m-d\TH:i:sP', // 2023-10-05T14:48:00+00:00
            'Y-m-d\TH:i:sO', // 2023-10-05T14:48:00+0000
            'Y-m-d H:i:sP',  // 2023-10-05 14:48:00+00:00
            'Y-m-d H:i:sO',  // 2023-10-05 14:48:00+0000
            'Y-m-d H:i:s T', // 2023-10-05 14:48:00 UTC
            'Y-m-d H:i:s e', // 2023-10-05 14:48:00 Europe/London
            'Y-m-d H:i:s',   // 2023-10-05 14:48:00
            'Y-m-d\TH:i:s',  // 2023-10-05T14:48:00
        ];

        foreach ($formats as $format) {
            $dateTime = \DateTime::createFromFormat($format, $date);
            if ($dateTime && $dateTime->format($format) === $date) {
                return $format;
            }
        }

        return 'unknown';
    }

    /**
     * Convierte una fecha en formato 'Y-m-d H:i:s' a segundos desde la época Unix.
     *
     * @param string $date La fecha en formato 'Y-m-d H:i:s'.
     * @return int El número de segundos desde la época Unix.
     */
    public static function convertDateToSeconds($date)
    {
        return strtotime($date);
    }

    /**
     * Convierte una fecha en formato UTC a una fecha en formato 'Y-m-d'.
     *
     * @param string $dateUtc Fecha en formato UTC (ejemplo: 20070724T224556Z).
     * @return string Fecha en formato 'Y-m-d'.
     */
    public static function convertUTCToDate($dateUtc)
    {
        $dateTime = \DateTime::createFromFormat('Ymd\THis\Z', $dateUtc, new \DateTimeZone('UTC'));
        if ($dateTime === false) {
            throw new \Exception("Invalid UTC date format: $dateUtc");
        }
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Convierte una fecha en formato 'Y-m-d H:i:s' a formato UTC.
     *
     * @param string $date La fecha en formato 'Y-m-d H:i:s'.
     * @return string La fecha en formato UTC (ejemplo: 20070724T224556Z).
     */
    public static function convertDateToUTC($date)
    {
        $dateTime = new \DateTime($date, new \DateTimeZone('UTC'));
        return $dateTime->format('Ymd\THis\Z');
    }

    /**
     * Convierte una fecha y hora a una zona horaria específica.
     *
     * @param string $datetime La fecha y hora en formato 'Y-m-d H:i:s'.
     * @param string $timezone La zona horaria a la que se desea convertir. Por defecto es 'UTC'.
     * @return string La fecha y hora convertida a la zona horaria especificada.
     * @throws \Exception Si ocurre un error al crear el objeto DateTime.
     */
    public static function convertToTimeZone($datetime, $timezone = 'America/Mexico_City')
    {
        $date = new \DateTime($datetime, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Convierte un timestamp a una fecha en formato 'Y-m-d H:i:s'.
     *
     * @param int $timestamp El timestamp a convertir.
     * @return string La fecha en formato 'Y-m-d H:i:s'.
     */
    public static function convertTimestampToDate($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Convierte una fecha en formato 'Y-m-d H:i:s' a un timestamp.
     *
     * @param string $date La fecha en formato 'Y-m-d H:i:s'.
     * @return int El timestamp correspondiente a la fecha.
     */
    public static function convertDateToTimestamp($date)
    {
        return strtotime($date);
    }

    /**
     * Establece la zona horaria y la configuración regional.
     *
     * Esta función configura la zona horaria predeterminada y la configuración regional
     * utilizando los valores definidos en la clase Settings.
     *
     * @return void
     */
    public static function setLocateTimeZone()
    {
        date_default_timezone_set(Settings::$timeZone);
        setlocale(LC_ALL, Settings::$locale);
        setlocale(LC_TIME, Settings::$locale);
    }
}
