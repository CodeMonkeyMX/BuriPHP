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

abstract class HelperValidate
{
    /**
     * Verifica si un valor está vacío.
     *
     * Esta función determina si un valor dado debe considerarse vacío.
     * Los siguientes valores se consideran no vacíos:
     * - `false`
     * - Objetos
     * - `null`
     * - El string '0'
     * 
     * Los siguientes valores se consideran vacíos:
     * - Arrays con longitud 0
     * - Los strings '0000-00-00', '0000-00-00 00:00:00', '00:00:00', 'null'
     * - Strings vacíos
     *
     * @param mixed $value El valor a verificar.
     * @return bool `true` si el valor está vacío, `false` en caso contrario.
     */
    public static function isEmpty($value): bool
    {
        if ($value === false || is_object($value) || is_null($value)) {
            return false;
        }
        if (is_array($value) && count($value) === 0) {
            return true;
        }
        if (is_array($value) && count($value) !== 0) {
            return false;
        }
        $tmp = strtolower(trim('' . $value));
        if ($tmp === '0') {
            return false;
        }

        if (
            $tmp === '0000-00-00' ||
            $tmp === '0000-00-00 00:00:00' ||
            $tmp === '00:00:00' ||
            $tmp === 'null'
        ) {
            return true;
        }
        return ($tmp === '');
    }

    /**
     * Verifica si una cadena es un correo electrónico válido.
     *
     * Utiliza la función filter_var con el filtro FILTER_VALIDATE_EMAIL para 
     * validar el formato del correo electrónico.
     *
     * @param string $sEmail La cadena que se va a verificar.
     * @return bool Devuelve true si la cadena es un correo electrónico válido, 
     *              de lo contrario, devuelve false.
     */
    public static function isEmail($sEmail)
    {
        return !(filter_var($sEmail, FILTER_VALIDATE_EMAIL) === false);
    }

    /**
     * Verifica si el dispositivo es un dispositivo móvil.
     *
     * Esta función comprueba el agente de usuario (user agent) para determinar si 
     * el dispositivo es uno de los siguientes: iPhone, iPod, iPad, Android, 
     * BlackBerry, WebOS o Windows Phone.
     *
     * @return bool Devuelve true si el dispositivo es un dispositivo móvil, 
     *              de lo contrario, devuelve false.
     */
    public static function isMobileDevice(): bool
    {
        $userAgent = strtolower(HelperServer::getValue('HTTP_USER_AGENT'));
        $mobileDevices = ['iphone', 'ipod', 'ipad', 'android', 'blackberry', 'webos', 'windows phone'];

        foreach ($mobileDevices as $device) {
            if (strpos($userAgent, $device) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si la solicitud HTTP es de tipo POST.
     *
     * @return bool Devuelve true si la solicitud es de tipo POST, de lo contrario false.
     */
    public static function isPostRequest(): bool
    {
        return strtoupper(HelperServer::getValue('REQUEST_METHOD')) === 'POST';
    }

    /**
     * Verifica si la solicitud HTTP es de tipo PUT.
     *
     * @return bool Devuelve true si la solicitud es de tipo PUT, de lo contrario false.
     */
    public static function isPutRequest(): bool
    {
        return strtoupper(HelperServer::getValue('REQUEST_METHOD')) === 'PUT';
    }

    /**
     * Verifica si la solicitud HTTP es de tipo UPDATE.
     *
     * @return bool Devuelve true si la solicitud es de tipo UPDATE, de lo contrario false.
     */
    public static function isUpdateRequest(): bool
    {
        return strtoupper(HelperServer::getValue('REQUEST_METHOD')) === 'UPDATE';
    }

    /**
     * Verifica si la solicitud HTTP es de tipo DELETE.
     *
     * @return bool Devuelve true si la solicitud es de tipo DELETE, de lo contrario false.
     */
    public static function isDeleteRequest(): bool
    {
        return strtoupper(HelperServer::getValue('REQUEST_METHOD')) === 'DELETE';
    }

    /**
     * Verifica si la solicitud HTTP es de tipo GET.
     *
     * @return bool Devuelve true si la solicitud es de tipo GET, de lo contrario false.
     */
    public static function isGetRequest(): bool
    {
        return strtoupper(HelperServer::getValue('REQUEST_METHOD')) === 'GET';
    }

    /**
     * Verifica si el nombre de archivo dado corresponde a un directorio.
     *
     * @param string $filename El nombre del archivo o ruta a verificar.
     * @return bool Devuelve true si el nombre de archivo es un directorio, de lo contrario false.
     */
    public static function isDir($filename)
    {
        return is_dir($filename);
    }

    /**
     * Verifica si una cadena es un GUID válido.
     *
     * @param string $guid La cadena a verificar.
     * @return bool Devuelve true si la cadena es un GUID válido, de lo contrario, false.
     */
    public static function isGUID($guid)
    {
        if (36 != strlen($guid)) {
            return false;
        }
        return (preg_match(
            "/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/i",
            $guid
        ));
    }

    /**
     * Verifica si un objeto es una instancia de una clase específica.
     *
     * @param object $obj El objeto a verificar.
     * @param string $className El nombre de la clase a comparar.
     * @return bool Devuelve true si el objeto es una instancia de la clase especificada, de lo contrario false.
     */
    public static function isClassOf($obj, $className): bool
    {
        return strtoupper(get_class($obj)) == strtoupper($className);
    }

    /**
     * Verifica si una palabra específica existe dentro de un texto dado.
     *
     * @param string $txt El texto en el cual se buscará la palabra.
     * @param string $wordSearch La palabra que se desea buscar en el texto.
     * @return bool Retorna true si la palabra existe en el texto, false en caso contrario.
     */
    public static function existWord($txt, $wordSearch)
    {
        $ret = preg_match("/\b" . $wordSearch . "\b/i", $txt);
        if (false === $ret) {
            return false;
        }

        return 0 !== $ret;
    }

    /**
     * Verifica si una cadena de texto contiene caracteres de nueva línea (CRLF).
     *
     * @param string $txt La cadena de texto a verificar.
     * @return bool Devuelve true si la cadena contiene CRLF, de lo contrario false.
     */
    public static function existCRLF($txt)
    {
        // false => error
        $ret = preg_match("/(%0A|%0D|\\n+|\\r+)/i", $txt);
        if (false === $ret) {
            return false;
        }
        // 0 si no coincide
        return 0 !== $ret;
    }

    /**
     * Verifica si una cadena comienza con un prefijo específico.
     *
     * Esta función comprueba si la cadena dada ($str) comienza con el prefijo especificado ($begin).
     * La comparación se realiza sin tener en cuenta las mayúsculas y minúsculas y sin acentos.
     *
     * @param string $str La cadena en la que se buscará el prefijo.
     * @param string $begin El prefijo que se buscará al inicio de la cadena.
     * @return bool Devuelve true si la cadena comienza con el prefijo especificado, de lo contrario, devuelve false.
     */
    public static function beginsWith($str, $begin): bool
    {
        $len = strlen($begin);
        if ($len > 0) {
            return (0 == strncasecmp(
                HelperString::removeAccents($str),
                HelperString::removeAccents($begin),
                strlen($begin)
            )
            );
        }
        return false;
    }

    /**
     * Verifica si una cadena termina con una subcadena específica.
     *
     * @param string $str La cadena en la que se buscará el final.
     * @param string $end La subcadena que se espera al final de $str.
     * @return bool Devuelve true si $str termina con $end, de lo contrario false.
     */
    public static function endsWith($str, $end): bool
    {
        $len = strlen($end);
        $lenStr = strlen($str);
        if ($len > 0  && $lenStr > 0 && ($lenStr - $len) > 0) {
            $str = substr($str, $lenStr - $len);
            return (0 == strcasecmp(
                HelperString::removeAccents($str),
                HelperString::removeAccents($end)
            ));
        }
        return false;
    }

    /**
     * Verifica si una cadena es una URL válida.
     *
     * @param string $url La URL a validar.
     * @param bool $protocol (Opcional) Si es true, la URL debe incluir el protocolo (http, https, ftp).
     * @return bool Devuelve true si la URL es válida, false en caso contrario.
     */
    public static function isUrl($url, $protocol = false)
    {
        // Carácteres permitidos
        $chars = '[a-z0-9\/:_\-_\.\?\$,;~=#&%\+]';
        if ($protocol) {
            return preg_match(
                "/^(http|https|ftp):\/\/" . $chars . "+$/i",
                $url
            );
        } else {
            return preg_match("/^" . $chars . "+$/i", $url);
        }
    }

    /**
     * Verifica si la solicitud es una petición AJAX.
     *
     * Esta función comprueba si el valor de la cabecera 'HTTP_X_REQUESTED_WITH' 
     * no está vacío y si es igual a 'xmlhttprequest' (en minúsculas).
     *
     * @return bool Devuelve true si la solicitud es una petición AJAX, de lo contrario false.
     */
    public static function ajaxRequest()
    {
        if (
            !self::isEmpty(HelperServer::getValue('HTTP_X_REQUESTED_WITH')) &&
            strtolower(HelperServer::getValue('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest'
        ) {
            return true;
        }

        return false;
    }
}
