<?php

namespace Libraries\BuriPHP;

use BuriPHP\Settings;
use Medoo\Medoo;

/**
 * Clase Database
 * 
 * Esta clase proporciona métodos para interactuar con la base de datos.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 1.0
 * @version 2.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Database
{
    /**
     * Crea una nueva instancia de la base de datos utilizando las opciones proporcionadas o los valores predeterminados de configuración.
     *
     * @param array $options Opciones para configurar la conexión a la base de datos. Las claves posibles son:
     *                       - 'type' (string): El tipo de base de datos (por defecto Settings::$dbType).
     *                       - 'host' (string): El host de la base de datos (por defecto Settings::$dbHost).
     *                       - 'database' (string): El nombre de la base de datos (por defecto Settings::$dbName).
     *                       - 'username' (string): El nombre de usuario para la base de datos (por defecto Settings::$dbUser).
     *                       - 'password' (string): La contraseña para la base de datos (por defecto Settings::$dbPass).
     *                       - 'charset' (string): El conjunto de caracteres para la conexión (por defecto Settings::$dbCharset).
     *                       - 'port' (int): El puerto para la conexión a la base de datos (por defecto Settings::$dbPort).
     *                       - 'prefix' (string): El prefijo para las tablas de la base de datos (por defecto Settings::$dbPrefix).
     *
     * @return Medoo Una nueva instancia de la clase Medoo configurada con los parámetros proporcionados.
     */
    final public function newInstance($options = [])
    {
        $arr = [
            // [required]
            'type' => isset($options['type']) ? $options['type'] : Settings::$dbType,
            'host' => isset($options['host']) ? $options['host'] : Settings::$dbHost,
            'database' => isset($options['database']) ? $options['database'] : Settings::$dbName,
            'username' => isset($options['username']) ? $options['username'] : Settings::$dbUser,
            'password' => isset($options['password']) ? $options['password'] : Settings::$dbPass,

            // [optional]
            'charset' => isset($options['charset']) ? $options['charset'] : Settings::$dbCharset,
            'port' => isset($options['port']) ? $options['port'] : Settings::$dbPort,

            // [optional] The table prefix. All table names will be prefixed as PREFIX_table.
            'prefix' => isset($options['port']) ? $options['port'] : Settings::$dbPrefix
        ];

        return new Medoo([
            'type' => $arr['type'],
            'host' => $arr['host'],
            'database' => $arr['database'],
            'username' => $arr['username'],
            'password' => $arr['password']
        ]);
    }

    /**
     * Convierte nombres de variables de camelCase a snake_case.
     *
     * @param mixed $data Puede ser un array o una cadena de texto.
     * 
     * @return mixed Si $data es un array, devuelve un array con las claves convertidas a snake_case.
     *               Si $data es una cadena de texto, devuelve la cadena convertida a snake_case.
     *               Si $data ya está en mayúsculas, devuelve la cadena tal cual.
     */
    final static function camelToSnake($data)
    {
        if (is_array($data)) {
            $response = [];

            foreach ($data as $key => $value) {
                if ($key !== strtoupper($key)) {
                    $response[preg_replace('/(?<!^)[A-Z]/', '_$0', $key)] = $value;
                } else {
                    $response[$key] = $value;
                }
            }

            return array_change_key_case($response, CASE_UPPER);
        }

        if (ctype_upper($data)) {
            return strtoupper($data);
        }

        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $data));
    }

    /**
     * Convierte las claves de un array de snake_case a camelCase.
     *
     * Esta función toma un array o una cadena en formato snake_case y lo convierte a camelCase.
     * Si se proporciona un array, la función recorrerá cada elemento y convertirá las claves de snake_case a camelCase.
     * Si se proporciona una cadena, la función simplemente la convertirá a camelCase.
     *
     * @param array|string $data El array o cadena en formato snake_case que se desea convertir.
     * @return array|string El array o cadena convertido a camelCase.
     */
    final static function snakeToCamel($data)
    {
        if (is_array($data)) {
            $response = [];

            foreach ($data as $k => $v) {
                foreach ($v as $key => $value) {
                    if ($key !== lcfirst(ucwords($key))) {
                        $response[$k][lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($key)))))] = $value;
                    }
                }
            }

            return $response;
        }

        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($data)))));
    }
}
