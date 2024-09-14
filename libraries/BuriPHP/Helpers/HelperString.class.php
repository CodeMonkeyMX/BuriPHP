<?php

/**
 * @package BuriPHP.Libraries.Helpers
 * 
 * @abstract
 *
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

namespace Libraries\BuriPHP\Helpers;

abstract class HelperString
{
    /**
     * Convierte una cadena de texto a mayúsculas.
     *
     * Esta función utiliza `mb_strtoupper` si está disponible para manejar caracteres especiales.
     * Si `mb_strtoupper` no está disponible, utiliza `strtoupper`.
     *
     * @param string $txt La cadena de texto a convertir.
     * @return string La cadena de texto convertida a mayúsculas.
     */
    public static function toUpper($txt): string
    {
        if (function_exists('mb_strtoupper')) {
            // Convierte carcateres especiales
            return '' . mb_strtoupper($txt);
        }
        return '' . strtoupper($txt);
    }

    /**
     * Convierte una cadena de texto a minúsculas.
     *
     * Esta función utiliza `mb_strtolower` si está disponible para manejar caracteres especiales.
     * Si `mb_strtolower` no está disponible, utiliza `strtolower`.
     *
     * @param string $txt La cadena de texto a convertir.
     * @return string La cadena de texto convertida a minúsculas.
     */
    public static function toLower($txt): string
    {
        if (function_exists('mb_strtolower')) {
            // Convierte carcateres especiales
            return '' . mb_strtolower($txt);
        }
        return '' . strtolower($txt);
    }

    /**
     * Reemplaza la primera aparición de una subcadena en un texto.
     *
     * @param string $txt El texto en el que se realizará la búsqueda y reemplazo.
     * @param string $origin La subcadena que se desea reemplazar.
     * @param string $destination La subcadena con la que se reemplazará la subcadena original.
     * @return string El texto resultante después de realizar el reemplazo.
     */
    public static function replaceFirstOccurrence($txt, $origin, $destination): string
    {
        $pos = strpos($txt, $origin);
        if ($pos !== false) {
            return substr_replace($txt, $destination, $pos, strlen($origin));
        }
        return $txt;
    }

    /**
     * Reemplaza todas las ocurrencias de una subcadena en un texto dado, sin distinguir entre mayúsculas y minúsculas.
     *
     * @param string $txt El texto en el que se realizarán las sustituciones.
     * @param string $origin La subcadena que se desea reemplazar.
     * @param string $destination La subcadena que reemplazará a la subcadena original.
     * @param int|null $numSuccess (Opcional) Una variable pasada por referencia que contendrá el número de reemplazos realizados.
     * @return string El texto resultante después de realizar todas las sustituciones.
     */
    public static function replaceAll($txt, $origin, $destination, &$numSuccess = null): string
    {
        return str_ireplace(
            '' . $origin,
            '' . $destination,
            '' . $txt,
            $numSuccess
        );
    }

    /**
     * Encuentra la posición de la primera aparición de una subcadena en una cadena, 
     * comenzando la búsqueda en una posición específica.
     *
     * @param string $txt La cadena en la que se buscará.
     * @param string $occurrence La subcadena cuya posición se desea encontrar.
     * @param int $posStartSearch La posición inicial desde donde comenzar la búsqueda. Por defecto es 0.
     * @return int La posición de la primera aparición de la subcadena, o -1 si no se encuentra.
     */
    public static function indexOf($txt, $occurrence, $posStartSearch = 0): int
    {
        $txt       = self::removeAccents($txt);
        $occurrence = self::removeAccents($occurrence);
        $pos       = stripos($txt, $occurrence, $posStartSearch);
        if ($pos === false) {
            return -1;
        }
        return $pos;
    }

    /**
     * Encuentra la última posición de una ocurrencia en una cadena de texto.
     *
     * @param string $txt La cadena de texto en la que se buscará.
     * @param string $occurrence La subcadena cuya última ocurrencia se desea encontrar.
     * @param int $posEndSearch [opcional] La posición desde el final de la cadena donde se debe detener la búsqueda. Por defecto es 0.
     * @return int La posición de la última ocurrencia de la subcadena en la cadena de texto, o -1 si no se encuentra.
     */
    public static function indexOfLast($txt, $occurrence, $posEndSearch = 0): int
    {
        $pos = strripos($txt, $occurrence, -1 * $posEndSearch);
        if ($pos === false) {
            return -1;
        }
        return $pos;
    }

    /**
     * Elimina caracteres de tabulación, retorno de carro y nueva línea de una cadena.
     *
     * @param string $str La cadena a sanitizar.
     * @return string La cadena sin caracteres de tabulación, retorno de carro y nueva línea.
     */
    public static function sanitizeTabReturn($str): string
    {
        return preg_replace('/[\n\r\t]+/', '', $str);
    }

    /**
     * Elimina espacios en blanco adicionales de una cadena.
     *
     * Esta función toma una cadena de texto y reemplaza cualquier secuencia de 
     * dos o más espacios en blanco con un solo espacio. Luego, elimina los 
     * espacios en blanco al principio y al final de la cadena.
     *
     * @param string $str La cadena de texto a ser sanitizada.
     * @return string La cadena de texto con los espacios en blanco adicionales eliminados.
     */
    public static function sanitizeBlanks($str): string
    {
        return trim(preg_replace('/\s{2,}/', ' ', $str));
    }

    /**
     * Sanitiza una cadena de texto eliminando acentos, caracteres especiales y 
     * caracteres no alfanuméricos.
     *
     * @param string $txt La cadena de texto a sanitizar.
     * @return string La cadena de texto sanitizada.
     */
    public static function sanitizeAll($txt): string
    {
        $txt = preg_replace('/[ ]{2}/', ' ', $txt);

        /* Elimina acentos, ñ y ç */
        $txt = self::removeAccents($txt);
        $txt = strtolower(strtr($txt, 'çñ ', 'cn_'));

        /* Elimina cualquier carácter no alfanumerico */
        return preg_replace('/[^A-Za-z0-9._\-]/', '', $txt);
    }

    /**
     * Obtiene la parte de la cadena a la derecha del delimitador especificado.
     *
     * @param string $txt La cadena de texto completa.
     * @param string $delimiter El delimitador a buscar dentro de la cadena.
     * @return string La parte de la cadena que se encuentra a la derecha del delimitador.
     */
    public static function getRightStringBack($txt, $delimiter)
    {
        $pos = stripos($txt, $delimiter);
        $ret = '';
        if (false !== $pos) {
            /* Busca el delimitador partiendo de la izquierda
             y si lo encuentra devuelve la parde de la la derecha */
            $ret = substr($txt, ($pos + strlen($delimiter)));
            if ($ret === false) {
                return '';
            }
        }
        return $ret;
    }

    /**
     * Obtiene la subcadena a la derecha de un delimitador específico.
     *
     * @param string $txt El texto completo del cual se extraerá la subcadena.
     * @param string $delimiter El delimitador que indica el punto de inicio de la subcadena.
     * @return string La subcadena a la derecha del delimitador. Si el delimitador no se encuentra, retorna una cadena vacía.
     */
    public static function getRightString($txt, $delimiter): string
    {
        $ret = '';
        $pos = stripos($txt, $delimiter);
        if ($pos !== false) {
            $ret = substr(
                $txt,
                $pos + strlen($delimiter),
                strlen($txt)
            );
            if ($ret === false) {
                return '';
            }
        }
        return $ret;
    }

    /**
     * Obtiene una subcadena de la longitud especificada desde el final de la cadena original.
     *
     * @param string $str La cadena original de la cual se extraerá la subcadena.
     * @param int $len La longitud de la subcadena que se desea obtener desde el final de la cadena original.
     * @return string La subcadena obtenida desde el final de la cadena original. Si la longitud especificada es mayor que la longitud de la cadena original, se devuelve la cadena completa. Si ocurre un error en la extracción de la subcadena, se devuelve una cadena vacía.
     */
    public static function getRightNum($str, $len): string
    {
        $len_str = strlen($str);
        if ($len > $len_str) {
            $ret = $str;
        } else {
            $ret = substr($str, $len_str - $len);
            if (false === $ret) {
                return '';
            }
        }
        return $ret;
    }

    /**
     * Obtiene una subcadena del texto dado, comenzando después del delimitador especificado y con la longitud especificada.
     *
     * @param string $txt El texto del cual se extraerá la subcadena.
     * @param string $delimiter El delimitador que indica el punto de inicio para la subcadena.
     * @param int $len La longitud de la subcadena a extraer.
     * @return string La subcadena extraída o una cadena vacía si el delimitador no se encuentra en el texto o si ocurre un error.
     */
    public static function getMiddleString($txt, $delimiter, $len): string
    {
        $ret = '';
        if (stripos($txt, $delimiter) !== false) {
            $pi  = stripos($txt, $delimiter) + strlen($delimiter);
            $ret = substr($txt, $pi, $len);
            if ($ret === false) {
                return '';
            }
        }
        return $ret;
    }

    /**
     * Obtiene una subcadena de una cadena dada, comenzando en una posición específica y con una longitud determinada.
     *
     * @param string $txt La cadena de la cual se extraerá la subcadena.
     * @param int $posStart La posición inicial desde donde se comenzará a extraer la subcadena.
     * @param int $len La longitud de la subcadena a extraer.
     * @return string La subcadena extraída. Si ocurre un error, se devuelve una cadena vacía.
     */
    public static function getMiddleNum($txt, $posStart, $len): string
    {
        $ret = substr($txt, $posStart, $len);
        if ($ret === false) {
            return '';
        }
        return $ret;
    }

    /**
     * Obtiene la parte izquierda de una cadena hasta el delimitador especificado.
     *
     * @param string $txt La cadena de texto de la cual se extraerá la parte izquierda.
     * @param string $delimiter El delimitador que indica hasta dónde se extraerá la cadena.
     * @return string La parte izquierda de la cadena hasta el delimitador, o una cadena vacía si el delimitador no se encuentra.
     */
    public static function getLeftString($txt, $delimiter): string
    {
        $pos = stripos($txt, $delimiter);
        $ret = '';
        if (false !== $pos) {
            $ret = substr($txt, 0, $pos);
            if ($ret === false) {
                return '';
            }
        }
        return $ret;
    }

    /**
     * Devuelve una subcadena desde una posición específica hasta el final de la cadena.
     *
     * @param string $txt La cadena de texto original.
     * @param int $len La posición desde donde se empezará a extraer la subcadena.
     * @return string La subcadena extraída desde la posición especificada hasta el final de la cadena.
     */
    public static function getRightNumBack($txt, $len): string
    {
        /* Empezando por la izquierda, devuelve la parte derecha */
        $ret = substr($txt, $len);
        if (false === $ret) {
            return '';
        }
        return $ret;
    }

    /**
     * Empezando por la izquierda, devuelve la parte de la izquierda de una cadena de texto.
     *
     * @param string $txt La cadena de texto de la cual se extraerá la parte izquierda.
     * @param int $len La longitud de la parte izquierda que se desea obtener.
     * @return string La parte izquierda de la cadena de texto especificada.
     */
    public static function getLeftNum($txt, $len): string
    {
        /* Empezando por la izquierda, devuelve la perte de la izquierda */
        $ret = substr('' . $txt, 0, $len);

        if (false === $ret) {
            return '';
        }
        return $ret;
    }

    /**
     * Obtiene el último carácter de una cadena de texto.
     *
     * @param string $txt La cadena de texto de la cual se obtendrá el último carácter.
     * @return string El último carácter de la cadena de texto. Si la cadena está vacía, retorna una cadena vacía.
     */
    public static function getLastChar($txt): string
    {
        if (HelperValidate::isEmpty($txt)) {
            return '';
        }
        return '' . substr('' . $txt, -1);
    }

    /**
     * Obtiene la cadena de texto que se encuentra entre dos subcadenas especificadas.
     *
     * @param string $txt El texto completo del cual se extraerá la subcadena.
     * @param string $strStart La subcadena que marca el inicio del texto a extraer.
     * @param string $strEnd La subcadena que marca el final del texto a extraer.
     * @return string|null La subcadena que se encuentra entre $strStart y $strEnd, 
     *                     o null si $strStart no se encuentra en $txt.
     */
    public static function getBetween($txt, $strStart, $strEnd)
    {
        $ret = null;

        if (stripos($txt, $strStart) !== false) {

            $pi  = stripos($txt, $strStart) + strlen($strStart);
            $lon = stripos($txt, $strEnd) - $pi;
            $ret = substr($txt, $pi, $lon);

            if (false === $ret) {
                return '';
            }
        }

        return $ret;
    }

    /**
     * Rellena una cadena a la derecha con un carácter especificado hasta alcanzar un tamaño dado.
     *
     * @param string $txt La cadena de texto a rellenar.
     * @param int $size El tamaño total deseado de la cadena después del relleno.
     * @param string $charPad (Opcional) El carácter con el que se rellenará la cadena. Por defecto es una cadena vacía.
     * @return string La cadena rellenada a la derecha con el carácter especificado.
     */
    public static function fillRight($txt, $size, $charPad = '')
    {
        return str_pad($txt, $size, $charPad, STR_PAD_RIGHT);
    }

    /**
     * Rellena una cadena a la izquierda con un carácter especificado hasta alcanzar un tamaño dado.
     *
     * @param string $txt La cadena de texto que se desea rellenar.
     * @param int $size El tamaño total que debe tener la cadena después de rellenar.
     * @param string $charPad (Opcional) El carácter con el que se rellenará la cadena. Por defecto es una cadena vacía.
     * @return string La cadena rellenada a la izquierda con el carácter especificado.
     */
    public static function fillLeft($txt, $size, $charPad = '')
    {
        return str_pad($txt, $size, $charPad, STR_PAD_LEFT);
    }

    /**
     * Elimina los acentos de una cadena dada.
     *
     * Esta función toma una cadena de texto y reemplaza los caracteres acentuados
     * con sus equivalentes sin acento. Funciona con caracteres en minúsculas y
     * mayúsculas, así como con caracteres con diéresis y circunflejos.
     *
     * @param string $str La cadena de texto de la cual se eliminarán los acentos.
     * @return string La cadena de texto sin acentos.
     */
    public static function removeAccents($str): string
    {
        return strtr(trim('' . $str), array(
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'à' => 'a',
            'è' => 'e',
            'ì' => 'i',
            'ò' => 'o',
            'ù' => 'u',
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'À' => 'A',
            'È' => 'E',
            'Ì' => 'I',
            'Ò' => 'O',
            'Ù' => 'U',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u',
            'Ä' => 'A',
            'Ë' => 'E',
            'Ï' => 'I',
            'Ö' => 'O',
            'Ü' => 'U',
            'â' => 'a',
            'ê' => 'e',
            'î' => 'i',
            'ô' => 'o',
            'û' => 'u',
            'Â' => 'A',
            'Ê' => 'E',
            'Î' => 'I',
            'Ô' => 'O',
            'Û' => 'U'
        ));
    }

    /**
     * Genera una cadena aleatoria de una longitud especificada.
     *
     * @param int $length La longitud de la cadena aleatoria a generar. Por defecto es 8.
     * @return string La cadena aleatoria generada.
     */
    public static function random($length = 8)
    {
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $base = strlen($salt);
        $stringRandom = '';

        $random = HelperCrypt::randomBytes($length + 1);
        $shift = ord($random[0]);

        for ($i = 1; $i <= $length; ++$i) {
            $stringRandom .= $salt[($shift + ord($random[$i])) % $base];
            $shift += ord($random[$i]);
        }

        return $stringRandom;
    }

    /**
     * Genera una clave única de 10 caracteres.
     *
     * Esta función crea una clave única utilizando `uniqid()` y `md5()`, 
     * y luego toma los primeros 10 caracteres del resultado.
     *
     * @return string Una cadena de 10 caracteres que representa la clave única generada.
     */
    public static function createKey(): string
    {
        return '' . substr(md5(uniqid()), 0, 10);
    }

    /**
     * Genera un GUID (Identificador Único Global) basado en la marca de tiempo actual.
     *
     * @return string El GUID generado.
     */
    public static function createGUID(): string
    {
        $microTime = microtime();

        list($arrDec, $arrSec) = explode(" ", $microTime);

        $decHex = (float) dechex($arrDec * 1000000);
        $secHex = dechex($arrSec);

        self::guidSetLen($decHex, 5);
        self::guidSetLen($secHex, 6);

        $guid = '';
        $guid .= $decHex;
        $guid .= self::guidCreateSection(3);
        $guid .= '-';
        $guid .= self::guidCreateSection(4);
        $guid .= '-';
        $guid .= self::guidCreateSection(4);
        $guid .= '-';
        $guid .= self::guidCreateSection(4);
        $guid .= '-';
        $guid .= $secHex;
        $guid .= self::guidCreateSection(6);

        return $guid;
    }

    /**
     * Ajusta la longitud de una cadena GUID a un tamaño específico.
     *
     * @param string &$string La cadena GUID que se va a ajustar.
     * @param int $length La longitud deseada para la cadena GUID.
     *
     * Si la longitud de la cadena es menor que la longitud deseada, se rellena con ceros ('0') hasta alcanzar la longitud deseada.
     * Si la longitud de la cadena es mayor que la longitud deseada, se trunca la cadena a la longitud deseada.
     */
    private static function guidSetLen(&$string, $length)
    {
        $strlen = strlen($string);
        if ($strlen < $length) {
            $string = str_pad($string, $length, '0');
        } else {
            if ($strlen > $length) {
                $string = substr($string, 0, $length);
            }
        }
    }

    /**
     * Crea una sección de un GUID (Identificador Único Global) de la longitud especificada.
     *
     * @param int $len La longitud de la sección del GUID a generar.
     * @return string Una cadena hexadecimal que representa la sección del GUID.
     */
    private static function guidCreateSection($len)
    {
        $ret = "";
        for ($i = 0; $i < $len; $i++) {
            $ret .= dechex(mt_rand(0, 15));
        }
        return $ret;
    }

    /**
     * Envuelve una cadena de texto a un número específico de caracteres utilizando un delimitador opcional.
     *
     * @param string $txt El texto que se va a envolver.
     * @param int $len El número de caracteres en el que se debe envolver el texto.
     * @param string $break (Opcional) El delimitador que se utilizará para envolver el texto. Si no se proporciona, se utilizará el valor predeterminado.
     * @return string El texto envuelto.
     */
    public static function wordWrap($txt, $len, $break = ''): string
    {
        if (empty($break)) {
            return wordwrap($txt, $len);
        } else {
            return wordwrap($txt, $len, $break);
        }
    }

    /**
     * Trunca una cadena de texto a una longitud específica y agrega un relleno si es necesario.
     *
     * @param string $txt La cadena de texto a truncar.
     * @param int $len La longitud máxima de la cadena truncada.
     * @param string $fill (Opcional) El texto de relleno que se agregará al final de la cadena truncada. Por defecto es '...'.
     * @return string La cadena truncada con el relleno agregado si es necesario.
     */
    public static function truncate($txt, $len, $fill = '...'): string
    {
        if (HelperValidate::isEmpty($txt)) {
            return '';
        }
        if (strlen($txt) > $len) {
            return '' . substr($txt, 0, $len) . $fill;
        }
        return $txt;
    }

    /**
     * Elimina las etiquetas HTML de un texto dado.
     *
     * @param string $txt El texto del cual se eliminarán las etiquetas HTML.
     * @param string|null $tagsDontRemove Una lista opcional de etiquetas HTML que no se deben eliminar.
     * @return string El texto sin las etiquetas HTML especificadas.
     */
    public static function removeHtmlTags($txt, $tagsDontRemove = null)
    {
        return strip_tags($txt, $tagsDontRemove);
    }
}
