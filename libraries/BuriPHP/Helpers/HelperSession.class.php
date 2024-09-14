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

abstract class HelperSession
{
    /**
     * Inicia una sesión PHP.
     *
     * Este método comprueba si no hay una sesión ya iniciada. Si no hay una sesión
     * iniciada y se proporciona un ID de sesión, se establece ese ID antes de iniciar
     * la sesión. Si ya hay una sesión iniciada y se proporciona un ID de sesión
     * diferente al actual, se destruye la sesión actual, se establece el nuevo ID de
     * sesión y se inicia una nueva sesión.
     *
     * @param string|null $sessionId El ID de sesión a utilizar, si se proporciona.
     * 
     * @return void
     */
    public static function init($sessionId = null): void
    {
        /*  Comprobar que no haya una sesión ya iniciada */
        if (PHP_SESSION_NONE == session_status()) {
            if (!empty($sessionId)) {
                session_id($sessionId);
            }
            session_start();
            return;
        }

        if (!empty($sessionId) && $sessionId != session_id()) {
            session_destroy();
            session_id($sessionId);
            session_start();
        }
    }

    /**
     * Establece un valor en la sesión.
     *
     * Serializa el valor proporcionado y lo almacena en la variable de sesión
     * asociada con la clave especificada.
     *
     * @param string $key La clave con la que se almacenará el valor en la sesión.
     * @param mixed $value El valor que se almacenará en la sesión. Si no se proporciona,
     *                     se almacenará null.
     */
    public static function setValue($key, $value = null)
    {
        $_SESSION[$key] = serialize($value);
    }

    /**
     * Obtiene el valor de una clave específica de la sesión.
     *
     * @param string $key La clave de la cual se desea obtener el valor.
     * @return mixed|null El valor deserializado almacenado en la sesión, o null si la clave no está definida.
     */
    public static function getValue($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        return unserialize($_SESSION[$key]);
    }

    /**
     * Obtiene una cadena de texto almacenada en la sesión.
     *
     * @param string $key La clave de la sesión para obtener el valor.
     * @return string|null La cadena de texto almacenada en la sesión, o null si no existe.
     */
    public static function getString($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        return strval(unserialize($_SESSION[$key]));
    }

    /**
     * Obtiene un valor entero de la sesión.
     *
     * @param string $key La clave del valor en la sesión.
     * @return int|null El valor entero almacenado en la sesión, o null si no existe.
     */
    public static function getInt($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        $tmpInt = unserialize($_SESSION[$key]);

        if (!is_numeric($tmpInt)) {
            return $tmpInt;
        }

        return intval($tmpInt);
    }

    /**
     * Obtiene un valor booleano de la sesión.
     *
     * @param string $key La clave del valor almacenado en la sesión.
     * @return bool|null Devuelve el valor booleano almacenado en la sesión, o null si la clave no existe.
     */
    public static function getBool($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }

        $tmpBool = unserialize($_SESSION[$key]);

        if (!is_bool($tmpBool)) {
            return $tmpBool;
        }
        return boolval($tmpBool);
    }

    /**
     * Destruye la sesión actual si está activa.
     *
     * Este método comprueba si la sesión está iniciada y, en caso afirmativo,
     * destruye la sesión y elimina todas las variables de sesión.
     *
     * @return void
     */
    public static function destroy(): void
    {
        /* Comprobar si la sesión está iniciada */
        if (PHP_SESSION_ACTIVE == session_status()) {
            session_destroy();
            session_unset();
        }
    }

    /**
     * Elimina un valor de la sesión basado en la clave proporcionada.
     *
     * @param string $key La clave del valor que se desea eliminar de la sesión.
     */
    public static function removeValue($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Verifica si existe un valor en la sesión para una clave dada.
     *
     * Esta función comprueba si una clave específica en la variable de sesión
     * está establecida y no está vacía.
     *
     * @param string $key La clave de la variable de sesión a verificar.
     * @return bool Devuelve true si la clave está establecida y no está vacía, de lo contrario false.
     */
    public static function existsValue($key)
    {
        return (isset($_SESSION[$key]) && !empty($_SESSION[$key])) ? true : false;
    }

    /**
     * Verifica si una sesión PHP está activa.
     *
     * Esta función comprueba el estado actual de la sesión y devuelve 
     * verdadero si la sesión está activa (PHP_SESSION_ACTIVE), de lo 
     * contrario, devuelve falso.
     *
     * @return bool Verdadero si la sesión está activa, falso en caso contrario.
     */
    public static function isActive()
    {
        return (PHP_SESSION_ACTIVE == session_status()) ? true : false;
    }

    /**
     * Verifica si la sesión del usuario ha caducado por inactividad.
     *
     * Este método comprueba si el usuario está autenticado y si la sesión ha 
     * estado inactiva por más tiempo del permitido. Si la sesión ha caducado, 
     * destruye la sesión y termina la ejecución del script.
     *
     * @return void
     */
    public static function isTimeOut()
    {
        /* Comprobar que el usuario esta autenticado */
        $minutesExpiration = 30;

        // Sólo si hay caducidad y esta logeado
        if ($minutesExpiration > 0) {
            $lastActionDate = self::getValue('_LAST_ACTION_SESSION_');

            /* Existe control de tiempo */
            if (!empty($lastActionDate)) {

                // Tiempo de inactividad
                $secondsInactive = time() - $lastActionDate;

                // Segundos de timeout
                $expireAfter = $minutesExpiration * 60;

                if ($secondsInactive >= $expireAfter) {
                    // Sesión caducada
                    session_destroy();
                    // redireccionar a otra página
                    die();
                }
            }

            self::setValue('_LAST_ACTION_SESSION_', time());
        }
    }
}
