<?php

/**
 * @package BuriPHP.Libraries
 *
 * @since 1.0
 * @version 2.4
 * @license You can see LICENSE.txt
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperLog;
use Libraries\BuriPHP\Helpers\HelperString;
use Libraries\BuriPHP\Helpers\HelperValidate;
use Libraries\BuriPHP\Interfaces\iController;

class Controller implements iController
{
    public $service;
    public $view;

    /**
     * Busca si existe el service del controller.
     * Si existe, lo inicializa.
     */
    final public function __construct(...$args)
    {
        if (method_exists($this, '__init')) {
            call_user_func_array(array($this, '__init'), []);
        }

        $controller = explode('\\', get_called_class());
        $controller = HelperArray::getLast($controller);

        $module = (isset($args['module']) && !empty($args['module'])) ? $args['module'] : Router::getEndpoint()[1]['MODULE'];

        if (HelperFile::exists(PATH_MODULES . $module . DS . $controller . SERVICE_PHP)) {
            require_once PATH_MODULES . $module . DS . $controller . SERVICE_PHP;

            $service = '\Services\\' . $controller;

            if (isset($args['module']) && !empty($args['module'])) {
                $this->service = new $service(module: $args['module']);
            } else {
                $this->service = new $service();
            }
        }

        $this->view = new View();
    }

    /**
     * Conecta un controlador de otro módulo.
     */
    final public function controllerShared($module, $controller)
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
             * Verifica que exista el controlador.
             */
            if (!HelperFile::exists(PATH_MODULES . $module . DS . $controller . CONTROLLER_PHP)) {
                $exceptionMsg = "No existe el controller: " . PATH_MODULES . $module . DS . $controller . CONTROLLER_PHP;

                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            } else {
                if (!class_exists('\Controllers\\' . $controller)) {
                    require PATH_MODULES . $module . DS . $controller . CONTROLLER_PHP;
                }

                $controller = '\Controllers\\' . $controller;

                return new $controller(module: $module);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Conecta un servicio de otro módulo.
     */
    final public function serviceShared($module, $service)
    {
        $serviceShared = new Service();
        return $serviceShared->serviceShared($module, $service);
    }

    /**
     * Obtiene los parametros enviados en la url.
     */
    final protected function getParams()
    {
        return Router::getEndpoint()[1]['PARAMS'];
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

    /**
     * @deprecated This function is deprecated and should not be used.
     * Obtiene la data e imagenes enviada por post.
     */
    final public function getPost()
    {
        return self::getPayload();
    }

    /**
     * @deprecated This function is deprecated and should not be used.
     * Obtiene la data enviada por put.
     */
    final public function getPut()
    {
        return self::getPayload();
    }

    /**
     * @deprecated This function is deprecated and should not be used.
     * Obtiene la data e imagenes enviada por patch.
     */
    final public function getPatch()
    {
        return self::getPayload();
    }

    /**
     * Obtiene los datos de la solicitud HTTP y los devuelve como un arreglo.
     *
     * @return array Los datos de la solicitud HTTP.
     */
    final public function getPayload()
    {
        $request = [];

        $rawData = self::parseRawData();
        if (!empty($rawData)) {
            $request = array_merge($request, $rawData);
        }

        if (!empty($_POST)) {
            $request = array_merge($request, $_POST);
        }

        if (isset($_FILES) && !empty($_FILES)) {
            $request = array_merge($request, $_FILES);
        }

        return $request;
    }

    /**
     * Función privada para analizar los datos sin procesar.
     *
     * Lee los datos sin procesar de la entrada PHP y los procesa según su tipo.
     * Si los datos son JSON, los decodifica y devuelve un array asociativo.
     * Si los datos son una cadena de consulta, los analiza y devuelve un array asociativo.
     * Si los datos son una carga de archivo, los guarda temporalmente y actualiza la variable global $_FILES.
     *
     * @return array Los datos procesados.
     */
    private static function parseRawData()
    {
        $_raw_data = fopen("php://input", "r");
        $raw_data = '';

        /* Read the data 1 KB at a time
       and write to the file */
        while ($chunk = fread($_raw_data, 1024))
            $raw_data .= $chunk;

        /* Close the streams */
        fclose($_raw_data);

        // Fetch content and determine boundary
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if (empty($boundary)) {
            json_decode($raw_data);
            if (json_last_error() === JSON_ERROR_NONE) {
                return json_decode($raw_data, true);
            } else {
                parse_str($raw_data, $data);
                return $data;
            }
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") break;

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if (isset($matches[4])) {
                    //if labeled the same as previous, skip
                    if (isset($_FILES[$matches[2]])) {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filename_parts = pathinfo($filename);
                    $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                    //populate $_FILES with information, size may be off in multibyte situation
                    $_FILES[$matches[2]] = array(
                        'error' => 0,
                        'name' => $filename,
                        'tmp_name' => $tmp_name,
                        'size' => strlen($body),
                        'type' => $value
                    );

                    //place in temporary directory
                    file_put_contents($tmp_name, $body);
                }
                //Parse Field
                else {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }
        }
        return $data;
    }
}
