<?php

namespace Libraries;

use Libraries\BuriPHP\Helpers\HelperHeader;
use Libraries\BuriPHP\Helpers\HelperValidate;

/**
 * Clase Responses
 * 
 * Esta clase se encarga de manejar las respuestas del sistema.
 * 
 * @package Libraries
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Responses
{
    /**
     * Genera una respuesta HTTP con el código de estado y datos opcionales.
     * 
     * Los códigos de estado soportados son:
     * - 200: OK
     * - 201: Created
     * - 202: Accepted
     * - 203: Non-Authoritative Information
     * - 204: No Content
     * - 205: Reset Content
     * - 206: Partial Content
     * - 300: Multiple Choices
     * - 301: Moved Permanently
     * - 302: Found
     * - 304: Not Modified
     * - 305: Use Proxy
     * - 307: Temporary Redirect
     * - 400: Bad Request
     * - 401: Unauthorized
     * - 403: Forbidden
     * - 404: Not Found
     * - 405: Method Not Allowed
     * - 406: Not Acceptable
     * - 407: Proxy Authentication Required
     * - 408: Request Timeout
     * - 409: Conflict
     * - 410: Gone
     * - 411: Length Required
     * - 412: Precondition Failed
     * - 413: Request Entity Too Large
     * - 414: Request-URI Too Long
     * - 415: Unsupported Media Type
     * - 416: Requested Range Not Satisfiable
     * - 417: Expectation Failed
     * - 500: Internal Server Error
     * - 501: Not Implemented
     * - 502: Bad Gateway
     * - 503: Service Unavailable
     * - 504: Gateway Timeout
     * - 505: HTTP Version Not Supported
     *
     * @param int $code Código de estado HTTP.
     * @param array $data Datos opcionales a incluir en la respuesta.
     * @return array Respuesta con el código de estado, el mensaje de estado y los datos opcionales.
     */
    public static function response(int $code, array $data = [])
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

        HelperHeader::setStatusCode($code);

        if (HelperValidate::isEmpty($data)) {
            return [
                'code' => $code,
                'status' => $status[$code]
            ];
        }

        return [
            'code' => $code,
            'status' => $status[$code],
            'data' => $data
        ];
    }
}
