<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperServer
 * 
 * Esta clase proporciona métodos auxiliares relacionados con el servidor.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.2
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperServer
{
    /**
     * Obtiene la URL actual del servidor.
     *
     * Este método construye y devuelve la URL completa actual del servidor,
     * incluyendo el dominio, el nombre del script y la cadena de consulta (query string) si existe.
     *
     * @return string La URL completa actual del servidor.
     */
    public static function getCurrentUrl()
    {
        $domain = self::getDomainHttp();
        $file   = self::getValue('SCRIPT_NAME');
        $qs     = HelperUrl::getQueryString();
        if (empty($qs)) {
            return $domain . $file;
        } else {
            return $domain . $file . '?' . $qs;
        }
    }

    /**
     * Obtiene la información actual del path.
     *
     * Este método verifica varias variables de entorno para determinar la información del path actual.
     * Primero, intenta obtener el valor de 'PATH_INFO'. Si no está disponible, intenta obtener 'ORIG_PATH_INFO'.
     * Si ninguno de estos valores está disponible, retorna una cadena vacía.
     *
     * Si se encuentra información del path, se divide en un array utilizando '/' como delimitador.
     * Luego, compacta el array y sanitiza cada valor del array.
     *
     * @return array Un array con la información del path actual, sanitizada y compactada.
     *               Si no hay información del path, retorna un array con un solo elemento "/".
     */
    public static function getCurrentPathInfo()
    {
        if (!empty(self::getValue('PATH_INFO'))) {
            $pathInfo = self::getValue('PATH_INFO');
        } else if (!empty(self::getValue('ORIG_PATH_INFO'))) {
            $pathInfo = self::getValue('ORIG_PATH_INFO');
        } else {
            $pathInfo = '';
        }

        if (!empty($pathInfo)) {
            $arrUrl = explode('/', $pathInfo);
            $arrUrl = HelperArray::compact($arrUrl);

            foreach ($arrUrl as $key => $value) {
                $arrUrl[$key] = HelperUrl::urlSanitized($value);
            }

            return $arrUrl;
        } else {
            return HelperConvert::toArray("/");
        }
    }

    /**
     * Obtiene el valor de una variable del servidor.
     *
     * Esta función busca el valor de una variable en el array superglobal $_SERVER.
     * Primero intenta obtener el valor usando la función filter_input con el filtro
     * FILTER_UNSAFE_RAW. Si no se encuentra la variable usando filter_input, intenta
     * obtener el valor directamente del array $_SERVER.
     *
     * @param string $key La clave de la variable del servidor que se desea obtener.
     * @return string El valor de la variable del servidor. Si la variable no existe,
     *                se devuelve una cadena vacía.
     */
    public static function getValue($key)
    {
        if (filter_has_var(INPUT_SERVER, $key)) {
            $value = filter_input(INPUT_SERVER, $key, FILTER_UNSAFE_RAW);
        } else {
            $value = '';
            if (array_key_exists($key, $_SERVER)) {
                $value = filter_var($_SERVER[$key], FILTER_UNSAFE_RAW);
            }
            return $value;
        }
        if ($value === false || is_null($value)) {
            return '';
        }
        return $value;
    }

    /**
     * Comprueba si el cliente acepta la compresión gzip.
     *
     * Esta función verifica el encabezado HTTP "Accept-Encoding" para determinar
     * si el cliente acepta la compresión gzip o x-gzip.
     *
     * @return bool Devuelve true si el cliente acepta gzip o x-gzip, de lo contrario false.
     */
    public static function compressionZip()
    {
        $acceptEnc = HelperServer::getValue('HTTP_ACCEPT_ENCODING');

        return (stripos($acceptEnc, 'gzip') >= 0 ||
            stripos($acceptEnc, 'x-gzip') >= 0);
    }

    /**
     * Obtiene el dominio de la URL actual.
     *
     * @return string El dominio de la URL actual.
     */
    public static function getDomain()
    {
        return HelperUrl::getHost(self::getCurrentUrl());
    }

    /**
     * Obtiene el dominio completo con el protocolo HTTP o HTTPS.
     *
     * @return string El dominio completo con el protocolo, o una cadena vacía si no se encuentra el host HTTP.
     */
    public static function getDomainHttp()
    {
        $httpHost = self::getValue('HTTP_HOST');
        if (empty($httpHost)) {
            return '';
        }

        return self::getProtocol() . "://" . $httpHost;
    }

    /**
     * Obtiene el protocolo utilizado por el servidor.
     *
     * Este método determina si el protocolo es HTTPS o HTTP basándose en las variables del servidor.
     * Verifica si la variable 'HTTPS' está presente y es 'on' o '1', o si la variable 'HTTP_X_FORWARDED_PROTO' 
     * está presente y es 'https'. Si alguna de estas condiciones se cumple, el protocolo es 'https', de lo contrario, es 'http'.
     *
     * @return string El protocolo utilizado ('https' o 'http').
     */
    public static function getProtocol()
    {
        return (!empty(self::getValue('HTTPS')) &&
            (self::getValue('HTTPS') == 'on' || self::getValue('HTTPS') == 1) ||
            !empty(self::getValue('HTTP_X_FORWARDED_PROTO')) &&
            self::getValue('HTTP_X_FORWARDED_PROTO') == 'https'
        ) ? "https" : "http";
    }

    /**
     * Obtiene la raíz del documento del servidor.
     *
     * @return string La raíz del documento del servidor.
     */
    public static function getDocumentRoot()
    {
        return self::getValue('DOCUMENT_ROOT');
    }

    /**
     * Obtiene todos los valores del arreglo $_SERVER.
     *
     * @return array Un arreglo asociativo con todas las claves y valores del arreglo $_SERVER.
     */
    public static function getAllValues()
    {
        $ret = [];
        foreach (array_keys($_SERVER) as $key) {
            $exists = filter_has_var(INPUT_SERVER, $key);
            if ($exists) {
                $value = filter_input(INPUT_SERVER, $key);
            } else {
                $value = filter_var(
                    $_SERVER[$key],
                    FILTER_DEFAULT
                );
            }
            $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * Obtiene las credenciales de autenticación del usuario.
     *
     * Este método asigna los valores de autenticación del usuario y la contraseña
     * a las variables proporcionadas por referencia.
     *
     * @param string &$user Variable que recibirá el nombre de usuario de autenticación.
     * @param string &$psw Variable que recibirá la contraseña de autenticación.
     */
    public static function getAuthentication(&$user, &$psw)
    {
        $user = self::getValue('PHP_AUTH_USER');

        $psw = self::getValue('PHP_AUTH_PW');
    }

    /**
     * Establece la configuración de reporte de errores y la visualización de errores en PHP.
     *
     * @param string $str Nivel de reporte de errores. Puede ser uno de los siguientes valores:
     *                    - 'none' o '0': No se reportan errores.
     *                    - 'simple': Se reportan errores simples (E_ERROR | E_WARNING | E_PARSE).
     *                    - 'maximum': Se reportan todos los errores (E_ALL).
     *                    - 'development': Se reportan todos los errores y se muestran (E_ALL y display_errors = 1).
     *                    - 'default' o '-1': Configuración por defecto (sin cambios).
     *
     * @return string Resultado de la configuración de error_reporting y ini_set.
     */
    public static function errorReporting($str)
    {
        $case = [];

        switch ($str) {
            case 'none':
            case '0':
                $case['error'] = '0';
                $case['ini'] = '0';
                break;

            case 'simple':
                $case['error'] = 'E_ERROR | E_WARNING | E_PARSE';
                $case['ini'] = '0';
                break;

            case 'maximum':
                $case['error'] = 'E_ALL';
                $case['ini'] = '1';
                break;

            case 'development':
                $case['error'] = '-1';
                $case['ini'] = '1';
                break;

            case 'default':
            case '-1':
            default:
                $case['error'] = '';
                $case['ini'] = '0';
                break;
        }

        return error_reporting($case['error']) . ini_set('display_errors', $case['ini']);
    }
}
