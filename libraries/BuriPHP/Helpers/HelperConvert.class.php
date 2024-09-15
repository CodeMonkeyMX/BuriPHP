<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperConvert
 * 
 * Esta clase proporciona métodos de conversión para diferentes tipos de datos.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.2
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperConvert
{
    /**
     * Convierte una cadena de texto a un valor booleano.
     *
     * @param mixed $strBool La cadena de texto o valor a convertir en booleano.
     *                        Puede ser una cadena, número o booleano.
     * @return bool|null Devuelve true si la cadena representa un valor verdadero 
     *                   ('S', 'Y', 'SI', 'YES', '1'), false si representa un valor 
     *                   falso ('N', 'NO', '0'), y null si no se puede determinar.
     */
    public static function toBool($strBool)
    {
        $ret = -1;
        if (HelperValidate::isEmpty($strBool)) {
            $ret = null;
        } else {
            if (is_string($strBool)) {
                $name = strtolower(strtr($strBool, 'ÍÓíó', 'ioio'));
                $sn = preg_replace('/^[:alnum:]/ui', '', strtoupper(trim($name)));
                if ($sn == 'S' || $sn == 'Y' || $sn == 'SI' || $sn == 'YES' || $sn == '1') {
                    $ret = true;
                } else {
                    if ($sn == 'N' || $sn == 'NO' || $sn == '0') {
                        $ret = false;
                    } else {
                        $ret = null;
                    }
                }
            } else {
                if (is_numeric($strBool)) {
                    if ($strBool === 1) {
                        $ret = true;
                    } else {
                        if ($strBool === 0) {
                            $ret = false;
                        } else {
                            $ret = null;
                        }
                    }
                } else {
                    if (is_bool($strBool)) {
                        return $strBool;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Convierte un elemento a un array.
     *
     * @param mixed $element El elemento a convertir.
     * 
     * @return array El elemento convertido a array. Si el elemento es un array, se devuelve tal cual.
     *               Si el elemento está vacío, se devuelve un array vacío.
     *               Si el elemento es un objeto, se convierte a array.
     *               En cualquier otro caso, se devuelve un array con el elemento como único valor.
     */
    public static function toArray($element): array
    {
        if (is_array($element)) {
            return $element;
        } elseif (HelperValidate::isEmpty($element)) {
            return [];
        } elseif (is_object($element)) {
            return (array)$element;
        } else {
            return array($element);
        }
    }

    /**
     * Convierte el texto proporcionado a HTML.
     *
     * Esta función toma una cadena de texto y la convierte a HTML seguro.
     * Si el texto no está vacío, se convierte cualquier carácter especial a su
     * entidad HTML correspondiente y se reemplazan los saltos de línea con etiquetas <br>.
     * Si el texto está vacío, se devuelve un espacio no separable (&nbsp;).
     *
     * @param string $txt El texto a convertir.
     * @return string El texto convertido a HTML.
     */
    public static function toHtml($txt)
    {
        return (!HelperValidate::isEmpty($txt)) ? nl2br(htmlspecialchars($txt)) : '&nbsp;';
    }

    /**
     * Convierte un valor numérico a formato de moneda.
     *
     * @param mixed $value El valor a convertir. Puede ser un número o una cadena que represente un número.
     * @param bool $simbol (Opcional) Indica si se debe agregar el símbolo de moneda. Por defecto es true.
     * @param string $simbolSign (Opcional) El símbolo de moneda a utilizar. Por defecto es '$'.
     * @return string|null La representación del valor en formato de moneda, o null si el valor está vacío.
     */
    public static function toCurrency($value, $simbol = true, $simbolSign = '$')
    {
        $f = floatval($value);

        $sSimbol = $simbol ? ' ' . $simbolSign : '';

        if (empty($f)) {
            return '0,00' . $sSimbol;
        }

        if ($f >= 0) {
            $sSign = '';
        } else {
            $f *= (-1);
            $sSign = '-';
        }

        $t = number_format($f, 2, ',', '.');

        return $sSign . $t . $sSimbol;
    }

    /**
     * Convierte un valor booleano a una cadena de texto "Sí" o "No".
     *
     * @param mixed $value El valor a convertir. Puede ser un booleano, un entero, o una cadena vacía.
     * @return string Retorna "Sí" si el valor es verdadero o 1, "No" si el valor es falso, 0 o vacío, 
     *                y retorna el valor original si no cumple con las condiciones anteriores.
     */
    public static function bool2YesNo($value)
    {
        if ($value === '' || is_null($value)) {
            return '';
        }

        if ($value == true || $value == 1) {
            return 'Sí';
        } else {
            if ($value == false || $value == 0 || empty($value)) {
                return 'No';
            } else {
                return $value;
            }
        }
    }

    /**
     * Convierte una cadena de texto a un número de punto flotante (double).
     *
     * @param string $txt La cadena de texto que se desea convertir.
     * @return double El valor numérico de punto flotante resultante de la conversión.
     */
    public static function string2Double($txt)
    {
        return doubleval(str_replace(',', '.', $txt));
    }

    /**
     * Convierte una cadena de texto en un array de palabras.
     *
     * @param string $str La cadena de texto a convertir.
     * @return array Un array de palabras obtenidas de la cadena de texto.
     */
    public static function string2ArrayWord($str)
    {
        return preg_split('/ /', $str, -1);
    }

    /**
     * Convierte una cadena en un array de caracteres.
     *
     * @param string $str La cadena que se desea convertir.
     * @return array Un array donde cada elemento es un carácter de la cadena original.
     */
    public static function string2ArrayChar($str)
    {
        return preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Convierte un número a una cadena con formato específico.
     *
     * @param float $num El número a convertir.
     * @param int $decimals El número de decimales a mostrar. Por defecto es 0.
     * @param bool $round Indica si se debe redondear el número. Por defecto es true.
     * @return string El número formateado como cadena.
     */
    public static function number2String($num, $decimals = 0, $round = true)
    {
        $sSepDecimals = '';

        if ($decimals != 0) {
            $sSepDecimals = ',';
        }

        $sSepMiles = '.';

        if (empty($num)) {
            return '0';
        }

        if (!$round) {
            return number_format($num, $decimals, $sSepDecimals, $sSepMiles);
        }

        $str = number_format($num, $decimals + 1, $sSepDecimals, $sSepMiles);

        return substr($str, 0, strlen($str) - 1);
    }

    /**
     * Convierte una cadena de tamaño en bytes.
     *
     * @param string $size La cadena que representa el tamaño, puede incluir sufijos como 'k', 'm', 'g' para kilobytes, megabytes y gigabytes respectivamente.
     * @return int|string El tamaño en bytes como un entero, o una cadena vacía si la entrada no es válida.
     */
    public static function number2Bytes($size)
    {
        $match    = null;
        $suffixes = array(
            ''  => 1,
            'k' => 1024,
            'm' => 1048576, /* 1024 * 1024 */
            'g' => 1073741824, /* 1024 * 1024 * 1024 */
        );

        if (preg_match('/([0-9]+)\s*(k|m|g)?(b?(ytes?)?)/i', $size, $match)) {
            return $match[1] * $suffixes[strtolower($match[2])];
        }

        return '';
    }

    /**
     * Convierte una cadena vacía en '0'.
     *
     * @param string $str La cadena a evaluar.
     * @return string '0' si la cadena está vacía, de lo contrario, retorna la cadena original.
     */
    public static function empty2Zero($str)
    {
        return HelperValidate::isEmpty($str) ? '0' : $str;
    }

    /**
     * Convierte un valor vacío en un espacio no separable (&nbsp;).
     *
     * @param mixed $val El valor a evaluar.
     * @return mixed Devuelve '&nbsp;' si el valor está vacío, de lo contrario devuelve el valor original.
     */
    public static function empty2Nbsp($val)
    {
        return HelperValidate::isEmpty($val) ? '&nbsp;' : $val;
    }

    /**
     * Decodifica las entidades HTML en una cadena de texto.
     *
     * @param string $txt La cadena de texto que contiene entidades HTML a decodificar.
     * @return string La cadena de texto con las entidades HTML decodificadas.
     */
    public static function decodeEntities($txt)
    {
        return html_entity_decode($txt);
    }

    /**
     * Convierte etiquetas <br> en saltos de línea (\n) en una cadena dada.
     *
     * @param string $str La cadena que contiene las etiquetas <br> a convertir.
     * @return string La cadena con las etiquetas <br> convertidas en saltos de línea (\n).
     */
    public static function br2nl($str)
    {
        if (empty($str)) {
            return $str;
        }

        preg_match_all("#<[^>]+br.+?>#i", $str, $matches);

        foreach ($matches[0] as $match) {
            $str = str_replace($match, "<br>", $str);
        }

        $brs = array('<br>', '<br/>', '<br />');
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\n\r", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_ireplace($brs, "\n", $str);

        return $str;
    }
}
