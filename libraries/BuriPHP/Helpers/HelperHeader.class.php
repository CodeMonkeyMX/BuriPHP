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

use Libraries\BuriPHP\Debug;

abstract class HelperHeader
{
    /**
     * Establece el código de estado HTTP y el texto asociado en la cabecera de la respuesta.
     *
     * @param int $code Código de estado HTTP. Por defecto es 200.
     * @param string $text Texto asociado al código de estado. Si no se proporciona, se utilizará el texto predeterminado.
     *
     * @return void
     *
     * @throws InvalidArgumentException Si el código de estado no es numérico.
     *
     * @example
     * HelperHeader::setStatusCode(404);
     * HelperHeader::setStatusCode(500, 'Error Interno del Servidor');
     *
     * @note Si no se proporciona un texto y el código de estado no es reconocido, se lanzará una alerta.
     */
    public static function setStatusCode($code = 200, $text = '')
    {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        if ($code == '' || !is_numeric($code)) {
            Debug::alert('Status codes must be numeric');
        }

        if (isset($status[$code]) and $text == '') {
            $text = $status[$code];
        }

        if ($text == '') {
            Debug::alert('No status text available.  Please check your status code number or supply your own message text.');
        }

        $serverProtocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            header("Status: {$code} {$text}", true);
        } elseif (
            $serverProtocol == 'HTTP/1.1'  ||
            $serverProtocol == 'HTTP/1.0'
        ) {
            header($serverProtocol . " {$code} {$text}", true, $code);
        } else {
            header("HTTP/1.1 {$code} {$text}", true, $code);
        }
    }

    /**
     * Redirige a una URL especificada.
     *
     * Esta función envía una cabecera HTTP para redirigir al usuario a la URL proporcionada.
     *
     * @param string $http La URL a la que se redirigirá al usuario.
     */
    public static function redirect($http)
    {
        header("Refresh: 0;url={$http}");
    }

    /**
     * Redirige al usuario a una URL específica y termina la ejecución del script.
     *
     * @param string $url La URL a la que se redirigirá al usuario.
     */
    public static function goLocation($url)
    {
        header('Location: ' . $url);
        die();
    }

    /**
     * Establece las cabeceras HTTP para indicar que el contenido ha expirado y no debe ser almacenado en caché.
     * 
     * Esta función envía las siguientes cabeceras:
     * - `Expires`: Fecha fija en el pasado para indicar que el contenido ha expirado.
     * - `Cache-Control`: Directivas para evitar el almacenamiento en caché del contenido.
     * - `Last-Modified`: Fecha y hora actuales en formato GMT.
     * - `Pragma`: Directiva para evitar el almacenamiento en caché.
     * 
     * @return void
     */
    public static function cacheExpired()
    {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2010 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        header("Pragma: no-cache");
    }

    /**
     * Borra la caché del navegador estableciendo los encabezados HTTP apropiados.
     *
     * Esta función envía los siguientes encabezados HTTP:
     * - "Cache-Control: no-cache": Indica que el navegador no debe almacenar en caché la respuesta.
     * - "Expires: -1": Indica que la respuesta ha expirado y no debe ser almacenada en caché.
     *
     * @return void
     */
    public static function clearCache()
    {
        header("Cache-Control: no-cache");
        header("Expires: -1");
    }

    /**
     * Establece las cabeceras HTTP para la autenticación básica.
     *
     * @param string $realm El nombre del ámbito (realm) para la autenticación.
     *
     * Esta función envía dos cabeceras HTTP:
     * - 'WWW-Authenticate' para solicitar la autenticación básica.
     * - 'HTTP/1.0 401 Unauthorized' para indicar que el acceso no está autorizado sin autenticación.
     */
    public static function setAutenticacion($realm)
    {
        header('WWW-Authenticate: Basic realm="' . $realm . '"');
        header('HTTP/1.0 401 Unauthorized');
    }

    /**
     * Establece el tipo de contenido en la cabecera HTTP.
     *
     * @param string $str El tipo de contenido a establecer. Los valores posibles son:
     *                    - 'html': text/html
     *                    - 'xhtml': text/xhtml+xml
     *                    - 'css': text/css
     *                    - 'javascript': text/javascript
     *                    - 'jpeg': image/jpeg
     *                    - 'svg': image/svg+xml
     *                    - 'webp': image/webp
     *                    - 'json': application/json
     *                    - 'pdf': application/pdf
     *                    - 'xml': application/xml
     *                    - 'plain': text/plain
     *
     * @return void
     */
    public static function setContentType($str)
    {
        switch ($str) {
            case 'html':
                header('Content-Type: text/html');
                break;
            case 'xhtml':
                header('Content-Type: text/xhtml+xml');
                break;
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'javascript':
                header('Content-Type: text/javascript');
                break;
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
            case 'webp':
                header('Content-Type: image/webp');
                break;
            case 'json':
                header('Content-Type: application/json');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'xml':
                header('Content-Type: application/xml');
                break;
            case 'plain':
                header('Content-Type: text/plain');
                break;
        }
    }
}
