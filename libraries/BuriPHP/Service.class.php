<?php

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperLog;
use Libraries\BuriPHP\Helpers\HelperValidate;

/**
 * Clase Service
 * 
 * Esta clase proporciona servicios esenciales para la aplicación.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.5
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Service
{
    /**
     * Repositorio utilizado por el servicio.
     *
     * @var mixed $repository
     */
    public $repository;

    /**
     * Constructor de la clase Service.
     *
     * Este constructor inicializa la clase y, si existe el método __init, lo llama.
     * Luego, determina el nombre del servicio y el módulo correspondiente.
     * Si el archivo del repositorio existe, lo requiere y crea una instancia del repositorio.
     *
     * @param mixed ...$args Argumentos opcionales que pueden incluir el módulo.
     */
    final public function __construct(...$args)
    {
        if (method_exists($this, '__init')) {
            call_user_func_array(array($this, '__init'), []);
        }

        $service = explode('\\', get_called_class());
        $service = HelperArray::getLastValue($service);

        $module = (isset($args['module']) && !empty($args['module'])) ? $args['module'] : Router::getEndpoint()[1]['MODULE'];

        if (HelperFile::exists(PATH_MODULES . $module . DS . $service . REPOSITORY_PHP)) {
            require_once PATH_MODULES . $module . DS . $service . REPOSITORY_PHP;

            $repository = '\Repositories\\' . $service;
            $this->repository = new $repository();
        }
    }

    /**
     * Método para compartir un servicio de un módulo específico.
     *
     * @param string $module El nombre del módulo.
     * @param string $service El nombre del servicio.
     * 
     * @return object Una instancia del servicio solicitado.
     * 
     * @throws \Exception Si el módulo o el servicio no existen.
     * @throws \Throwable Si ocurre cualquier otro error durante la ejecución.
     */
    final public function serviceShared($module, $service)
    {
        try {
            /**
             * Verifica que exista el módulo.
             */
            if (!HelperValidate::isDir(PATH_MODULES . $module)) {
                $exceptionMsg = "No existe el module: " . PATH_MODULES . $module;

                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            }

            /**
             * Verifica que exista el servicio.
             */
            if (!HelperFile::exists(PATH_MODULES . $module . DS . $service . SERVICE_PHP)) {
                $exceptionMsg = "No existe el service: " . PATH_MODULES . $module . DS . $service . SERVICE_PHP;

                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            } else {
                if (!class_exists('\Services\\' . $service)) {
                    require PATH_MODULES . $module . DS . $service . SERVICE_PHP;
                }

                $service = '\Services\\' . $service;

                return new $service(module: $module);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
