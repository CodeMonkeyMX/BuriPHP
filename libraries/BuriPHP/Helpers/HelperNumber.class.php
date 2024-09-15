<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperNumber
 * 
 * Esta clase proporciona métodos de ayuda relacionados con operaciones numéricas.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperNumber
{
    /**
     * Formatea un número como una cadena de texto que representa una cantidad monetaria.
     *
     * @param float  $number El número a formatear.
     * @param string $symbol (Opcional) El símbolo de la moneda a utilizar. Por defecto es una cadena vacía.
     *
     * @return string La representación formateada del número como una cantidad monetaria.
     *
     * Este método utiliza la configuración regional del sistema para determinar el formato de la moneda.
     * Si la configuración regional no está establecida, se establece a una configuración predeterminada.
     * 
     * Ejemplo de uso:
     * ```php
     * echo HelperNumber::displayMoney(1234.56, '$'); // Salida: $ 1,234.56
     * ```
     */
    public static function displayMoney($number, $symbol = '')
    {
        $ex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' . '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }

        $locale = localeconv();
        $format = '%i';
        preg_match_all($ex, $format, $matches, PREG_SET_ORDER);

        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match(
                    '/\=(.)/',
                    $fmatch[1],
                    $match
                ) ? $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match(
                    '/\+|\(/',
                    $fmatch[1],
                    $match
                ) ? $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left  = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];

            $conversion     = $fmatch[5];
            $positiveValue = true;

            if ($value < 0) {
                $positiveValue = false;
                $value          *= -1;
            }
            $letter = $positiveValue ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $sign = '';

            $sign = $positiveValue ? $locale['positive_sign'] : $locale['negative_sign'];

            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 &&
                    $flags['usesignal'] == '+':

                    $prefix = $sign;
                    break;

                case $locale["{$letter}_sign_posn"] == 2 &&
                    $flags['usesignal'] == '+':
                    $suffix = $sign;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 &&
                    $flags['usesignal'] == '+':
                    $cprefix = $sign;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 &&
                    $flags['usesignal'] == '+':
                    $csuffix = $sign;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix  = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix . ($conversion == 'i' ?
                    $locale['int_curr_symbol'] :
                    $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = $cprefix . $csuffix;
            }

            // Eliminamos el texto de la moneda 
            $currency = " " . $symbol . " ";
            $espacio = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format(
                $value,
                $right,
                $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']
            );
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) +
                strlen($currency) +
                strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat(
                    $flags['fillchar'],
                    $left - $n
                ) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $espacio . $value . $suffix;
            } else {
                $value = $prefix . $value . $espacio . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad(
                    $value,
                    $width,
                    $flags['fillchar'],
                    $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT
                );
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        $format = trim(str_replace('  ', ' ', $format));
        return $format;
    }

    /**
     * Extrae todos los números de una cadena dada.
     *
     * @param string|null $string La cadena de la cual se extraerán los números. Si es null, se devolverá null.
     * @return string|null Una cadena que contiene solo los números extraídos de la cadena original, o null si la entrada es null.
     */
    public static function getNumbers($string)
    {
        if (is_null($string)) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $string);
    }

    /**
     * Genera un número aleatorio de una longitud específica.
     *
     * @param int $numDigits La cantidad de dígitos que debe tener el número aleatorio. Por defecto es 6.
     * @return string El número aleatorio generado como una cadena de texto.
     */
    public static function random($numDigits = 6): string
    {
        $random = '';
        for ($n = $numDigits; $n > 0; $n--) {
            $generated = '' . mt_rand();
            $position = mt_rand(1, strlen($generated) - 1);
            $random   .= $generated[$position];
        }
        return strval($random);
    }

    /**
     * Genera un número aleatorio dentro de un rango especificado y lo devuelve como una cadena.
     *
     * @param int $min El valor mínimo del rango.
     * @param int $max El valor máximo del rango.
     * @return string El número aleatorio generado como una cadena.
     */
    public static function createRandomRange($min, $max): string
    {
        return '' . mt_rand($min, $max);
    }
}
