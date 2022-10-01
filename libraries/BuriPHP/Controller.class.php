<?php

/**
 * @package BuriPHP.Libraries
 *
 * @since 1.0
 * @version 2.2
 * @license You can see LICENSE.txt
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Interfaces\iController;

class Controller implements iController
{
    public $service;
    public $view;

    /**
     * Busca si existe el service del controller.
     * Si existe, lo inicializa.
     */
    final public function __construct()
    {
        $controller = explode('\\', get_called_class());
        $controller = HelperArray::getLast($controller);

        if (HelperFile::exists(PATH_MODULES . Router::getEndpoint()[1]['MODULE'] . DS . $controller . SERVICE_PHP)) {
            require_once PATH_MODULES . Router::getEndpoint()[1]['MODULE'] . DS . $controller . SERVICE_PHP;

            $service = '\Services\\' . $controller;
            $this->service = new $service();
        }

        $this->view = new View();
    }

    /**
     * Obtiene los parametros enviados en la url.
     */
    final protected function getParams()
    {
        return Router::getEndpoint()[1]['PARAMS'];
    }

    /**
     * Obtiene la data e imagenes enviada por post.
     */
    final public function getPost()
    {
        $request = [];

        if (!empty($_POST)) {
            // when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request
            // NOTE: if this is the case and $_POST is empty, check the variables_order in php.ini! - it must contain the letter P
            $request = array_merge($request, $_POST);
        }

        // when using application/json as the HTTP Content-Type in the request 
        $post = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() == JSON_ERROR_NONE) {
            $request = array_merge($request, $post);
        }

        if (isset($_FILES) && !empty($_FILES)) {
            $request = array_merge($request, $_FILES);
        }

        return $request;
    }

    /**
     * Obtiene la data enviada por get.
     */
    final public function getGet()
    {
        $request = [];

        if (!empty($_GET)) {
            // when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request
            $request = array_merge($request, $_GET);
        }

        return $request;
    }
}
