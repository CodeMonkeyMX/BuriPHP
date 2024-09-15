<?php

namespace Libraries\BuriPHP;

use BuriPHP\Settings;

/**
 * Clase Repository
 * 
 * Esta clase se encarga de manejar las operaciones de almacenamiento y recuperación de datos.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.2
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Repository
{
    /**
     * @var mixed $database La instancia de la base de datos utilizada por el repositorio.
     */
    public $database;

    /**
     * Constructor de la clase Repository.
     *
     * Este constructor realiza las siguientes acciones:
     * 1. Verifica si existe el método '__init' en la clase y lo llama si está presente.
     * 2. Si la configuración de la aplicación indica que se debe usar una base de datos (Settings::$useDatabase),
     *    crea una nueva instancia de la clase Database y la asigna a la propiedad $database.
     *
     * @final Este método no puede ser sobrescrito por clases hijas.
     */
    final public function __construct()
    {
        if (method_exists($this, '__init')) {
            call_user_func_array(array($this, '__init'), []);
        }

        if (Settings::$useDatabase) {
            $this->database = (new Database())->newInstance();
        }
    }

    /**
     * Crea una nueva instancia de la base de datos utilizando los argumentos proporcionados.
     *
     * @param array $arguments Los argumentos necesarios para crear una nueva instancia de la base de datos.
     * @return Database Una nueva instancia de la clase Database.
     */
    final public function newInstance($arguments)
    {
        return (new Database())->newInstance($arguments);
    }
}
