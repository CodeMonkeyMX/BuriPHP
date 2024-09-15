<?php

namespace Libraries;

use Libraries\BuriPHP\Helpers\HelperServer;
use Libraries\BuriPHP\Helpers\HelperSession;
use Libraries\BuriPHP\Helpers\HelperValidate;

/**
 * Clase Functions
 * 
 * Esta clase contiene una colección de funciones utilitarias que pueden ser utilizadas
 * en diferentes partes de la aplicación. Proporciona métodos estáticos para realizar
 * diversas operaciones comunes.
 * 
 * @package Libraries
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Functions
{
    /**
     * Obtiene la sesión actual basada en el token de autorización JWT.
     *
     * Este método verifica si hay un token JWT presente en la cabecera HTTP de autorización
     * o en la sesión actual. Si se encuentra un token y la variable global de sesión está 
     * configurada, retorna un arreglo con el token codificado y la sesión decodificada.
     * 
     * @return array|false Retorna un arreglo con el token JWT codificado y la sesión decodificada,
     *                     o false si no se encuentra un token válido o la sesión no está configurada.
     */
    public static function getSession()
    {
        if (!HelperValidate::isEmpty(HelperServer::getValue('HTTP_AUTHORIZATION'))) {
            $jwt = str_replace('Bearer ', '', HelperServer::getValue('HTTP_AUTHORIZATION'));
        } else if (!HelperValidate::isEmpty(HelperSession::existsValue('authorization'))) {
            $jwt = HelperSession::getString('authorization');
        }

        if (!is_null($jwt) && isset($GLOBALS['_APP']['SESSION'])) {
            return [
                "encode" => $jwt,
                "decode" => $GLOBALS['_APP']['SESSION']
            ];
        } else {
            return false;
        }
    }
}
