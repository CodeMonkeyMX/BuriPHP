<?php

/**
 * @package BuriPHP.Libraries
 *
 * @since 2.0Alpha
 * @version 1.4
 * @license You can see LICENSE.txt
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperLog;
use Libraries\BuriPHP\Helpers\HelperValidate;
use Libraries\BuriPHP\Interfaces\iService;

class Service implements iService
{
    public $repository;

    /**
     * Busca si existe el repository del controller.
     * Si existe, lo inicializa.
     */
    final public function __construct(...$args)
    {
        if (method_exists($this, '__init')) {
            call_user_func_array(array($this, '__init'), []);
        }

        $service = explode('\\', get_called_class());
        $service = HelperArray::getLast($service);

        $module = (isset($args['module']) && !empty($args['module'])) ? $args['module'] : Router::getEndpoint()[1]['MODULE'];

        if (HelperFile::exists(PATH_MODULES . $module . DS . $service . REPOSITORY_PHP)) {
            require_once PATH_MODULES . $module . DS . $service . REPOSITORY_PHP;

            $repository = '\Repositories\\' . $service;
            $this->repository = new $repository();
        }
    }

    /**
     * Conecta un servico de otro módulo.
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
