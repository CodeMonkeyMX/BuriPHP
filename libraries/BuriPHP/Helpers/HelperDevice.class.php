<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperDevice
 * 
 * Esta clase proporciona métodos para obtener información del dispositivo del usuario
 * a partir de la cadena del agente de usuario (User-Agent) y otras cabeceras HTTP.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 2.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperDevice
{
    /**
     * Obtiene el sistema operativo (SO) del agente de usuario.
     *
     * Este método analiza la cadena del agente de usuario (User-Agent) para determinar
     * el sistema operativo del cliente. Devuelve 'Linux', 'MacOS', 'Windows' u 'Otro'
     * dependiendo del sistema operativo detectado.
     *
     * @return string El nombre del sistema operativo detectado.
     */
    public static function getSOFromUserAgent()
    {
        $userAgent = HelperString::toLower(HelperServer::getValue('HTTP_USER_AGENT'));

        if (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            return 'MacOS';
        } elseif (preg_match('/windows|win32/i', $userAgent)) {
            return 'Windows';
        } else {
            return 'Otro';
        }
    }

    /**
     * Obtiene el nombre del navegador basado en el agente de usuario.
     *
     * Este método analiza la cadena del agente de usuario (user agent) para determinar
     * el nombre del navegador o dispositivo que realiza la solicitud.
     *
     * @return string El nombre del navegador o dispositivo detectado. Puede ser uno de los siguientes:
     *                - 'Opera'
     *                - 'Edge'
     *                - 'Chrome'
     *                - 'Safari'
     *                - 'Firefox'
     *                - 'IE' (Internet Explorer)
     *                - 'iPod'
     *                - 'iPhone'
     *                - 'iPad'
     *                - 'Android'
     *                - 'WebOS'
     *                - 'Blackberry'
     *                - 'Otro' (si no se detecta ningún navegador o dispositivo conocido)
     */
    public static function getBrowserName()
    {
        $userAgent = HelperString::toLower(HelperServer::getValue('HTTP_USER_AGENT'));

        if (strpos($userAgent, 'opera') || strpos($userAgent, 'opr/')) {
            return 'Opera';
        } elseif (stripos($userAgent, 'edge') !== false) {
            return 'Edge';
        } elseif (stripos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (stripos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (stripos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (
            stripos($userAgent, 'msie') !== false ||
            stripos($userAgent, 'trident/7') !== false
        ) {
            return 'IE';
        } elseif (stripos($userAgent, 'ipod') !== false) {
            return 'iPod';
        } elseif (stripos($userAgent, 'iphone') !== false) {
            return 'iPhone';
        } elseif (stripos($userAgent, 'ipad') !== false) {
            return 'iPad';
        } elseif (stripos($userAgent, 'android') !== false) {
            return 'Android';
        } elseif (stripos($userAgent, 'webos') !== false) {
            return 'WebOS';
        } elseif (stripos($userAgent, 'blackberry') !== false) {
            return 'Blackberry';
        }

        return 'Otro';
    }

    /**
     * Obtiene la dirección IP real del cliente.
     *
     * Este método verifica varias cabeceras HTTP para determinar la dirección IP real del cliente,
     * incluyendo soporte para proxies y servicios como Cloudflare.
     *
     * @return string|null La dirección IP real del cliente, o null si no se puede determinar.
     */
    public static function getRealIp()
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            $value = HelperServer::getValue($key);

            if (!HelperValidate::isEmpty($value)) {
                foreach (explode(',', $value) as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Obtiene el idioma del navegador del usuario a partir del encabezado HTTP_ACCEPT_LANGUAGE.
     *
     * @return string El idioma principal del navegador en minúsculas. Si no se encuentra el encabezado, 
     *                o si no se puede analizar, se devuelve 'es' como idioma predeterminado.
     */
    public static function getBrowserLanguage()
    {
        $httpAcceptLanguage = HelperServer::getValue('HTTP_ACCEPT_LANGUAGE');
        if (HelperValidate::isEmpty($httpAcceptLanguage)) {
            return 'es'; // Default language if none is found
        }

        $languages = explode(',', $httpAcceptLanguage);
        if (count($languages) > 0) {
            $primaryLanguage = explode(';', $languages[0]);
            return HelperString::toLower(trim($primaryLanguage[0]));
        }

        return 'es'; // Default language if parsing fails
    }
}
