<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperUrl
 * 
 * Esta clase proporciona métodos auxiliares relacionados con la manipulación y generación de URLs.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.2
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperUrl
{
    /**
     * Sanitiza una URL eliminando espacios en blanco y caracteres no deseados.
     *
     * @param string $str La URL a sanitizar.
     * @return string La URL sanitizada.
     */
    public static function urlSanitized($str)
    {
        if (!HelperValidate::isEmpty($str)) {
            $str = HelperString::sanitizeBlanks($str);
            $str = HelperString::sanitizeAll($str);
        }

        return $str;
    }

    /**
     * Obtiene el valor de un parámetro de la cadena de consulta (query string).
     *
     * @param string $parameter El nombre del parámetro a buscar en la cadena de consulta.
     * @return string|null El valor del parámetro si se encuentra, o null si no se encuentra.
     */
    public static function getQueryStringParam($parameter)
    {
        $matches = array();
        $qs      = self::getQueryString();
        $number  = preg_match("/{$parameter}=([a-zA-Z0-9_-]+)[&]?/", $qs, $matches);

        if ($number) {
            return '' . $matches[1];
        } else {
            return null;
        }
    }

    /**
     * Obtiene los parámetros de la cadena de consulta (query string) de la URL.
     *
     * @return array Un arreglo asociativo donde las claves son los nombres de los parámetros y los valores son los valores de los parámetros.
     */
    public static function getQueryStringParams()
    {
        $qs = self::getQueryString();
        if (HelperValidate::isEmpty($qs)) {
            return [];
        }
        $arrayTmp    = [];
        $arrayParams = explode('&', $qs);
        foreach ($arrayParams as $key => $value) {

            $b = explode('=', $arrayParams[$key]);

            $arrayTmp[$b[0]] = $b[1];
        }
        return $arrayTmp;
    }

    /**
     * Obtiene la cadena de consulta (query string) de la URL.
     *
     * @return string La cadena de consulta (query string).
     */
    public static function getQueryString()
    {
        return HelperServer::getValue('QUERY_STRING');
    }

    /**
     * Añade un parámetro a la cadena de consulta (query string) de la URL.
     *
     * @param string $url La URL a la que se añadirá el parámetro.
     * @param string $parameter El nombre del parámetro a añadir.
     * @param string $value El valor del parámetro a añadir.
     * @return string La URL con el parámetro añadido.
     */
    public static function addQueryStringParam($url, $parameter, $value)
    {
        $parsedUrl = parse_url($url);
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
        parse_str($query, $queryParams);
        $queryParams[$parameter] = $value;
        $newQuery = http_build_query($queryParams);
        $newUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['path'])) {
            $newUrl .= $parsedUrl['path'];
        }
        $newUrl .= '?' . $newQuery;
        return $newUrl;
    }

    /**
     * Obtiene el host de una URL dada.
     *
     * @param string $txt La URL de la cual se desea obtener el host.
     * @return string El host de la URL proporcionada.
     */
    public static function getHost($txt)
    {
        return parse_url($txt)['host'];
    }

    /**
     * Codifica una URL utilizando el esquema de codificación de URL.
     *
     * @param string $url La URL que se desea codificar.
     * @return string La URL codificada.
     */
    public static function encode($url)
    {
        return urlencode($url);
    }

    /**
     * Decodifica una URL codificada.
     *
     * @param string $urlEncoded La URL codificada que se desea decodificar.
     * @return string La URL decodificada.
     */
    public static function decode($urlEncoded)
    {
        return urldecode($urlEncoded);
    }
}
