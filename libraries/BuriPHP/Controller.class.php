<?php

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperLog;
use Libraries\BuriPHP\Helpers\HelperValidate;

/**
 * Clase Controller
 *
 * Esta clase representa un controlador base en el framework BuriPHP. Proporciona métodos para inicializar
 * servicios y vistas, compartir controladores y servicios, y obtener parámetros de solicitudes HTTP.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 1.0
 * @version 2.5
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Controller
{
    /**
     * Servicio utilizado por el controlador.
     *
     * @var mixed $service
     */
    public $service;

    /**
     * @var mixed $view La vista asociada al controlador.
     */
    public $view;

    /**
     * Constructor de la clase Controller.
     *
     * Este constructor inicializa el controlador y su servicio asociado, si existe.
     * También inicializa la vista para el controlador.
     *
     * @param array ...$args Argumentos opcionales para la inicialización del controlador.
     *                       - 'module': (opcional) El módulo específico a utilizar.
     *
     * @return void
     */
    final public function __construct(...$args)
    {
        if (method_exists($this, '__init')) {
            call_user_func_array(array($this, '__init'), []);
        }

        $controller = explode('\\', get_called_class());
        $controller = HelperArray::getLastValue($controller);

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
     * Método que comparte el controlador de un módulo específico.
     *
     * @param string $module El nombre del módulo.
     * @param string $controller El nombre del controlador.
     *
     * @return object Instancia del controlador solicitado.
     *
     * @throws \Exception Si el módulo o el controlador no existen.
     * @throws \Throwable Si ocurre cualquier otro error durante la ejecución.
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
     * Método final público que proporciona un servicio compartido.
     *
     * @param string $module El nombre del módulo.
     * @param string $service El nombre del servicio.
     * @return mixed El resultado del método serviceShared del objeto Service.
     */
    final public function serviceShared($module, $service)
    {
        $serviceShared = new Service();
        return $serviceShared->serviceShared($module, $service);
    }

    /**
     * Obtiene los parámetros de la ruta actual.
     *
     * Esta función protegida y final devuelve los parámetros asociados con la 
     * ruta actual utilizando el método `getEndpoint` de la clase `Router`.
     *
     * @return array Los parámetros de la ruta actual.
     */
    final protected function getParams()
    {
        return Router::getEndpoint()[1]['PARAMS'];
    }

    /**
     * Obtiene los parámetros de la solicitud HTTP GET.
     *
     * Este método recopila los parámetros enviados a través de una solicitud HTTP GET
     * y los devuelve en un arreglo. Si no hay parámetros en la solicitud, se devuelve
     * un arreglo vacío.
     *
     * @return array Arreglo que contiene los parámetros de la solicitud GET.
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
     * Obtiene la carga útil de la solicitud.
     *
     * Este método recopila y combina datos de diferentes fuentes de la solicitud:
     * - Datos sin procesar (raw data) obtenidos mediante el método `parseRawData()`.
     * - Datos enviados a través del método POST.
     * - Archivos subidos a través de la solicitud.
     *
     * @return array La carga útil de la solicitud combinada.
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
     * Analiza los datos sin procesar de la entrada PHP y los convierte en un array asociativo.
     * 
     * Este método lee los datos sin procesar de la entrada PHP (`php://input`), determina si los datos están en formato JSON o en formato de formulario,
     * y los convierte en un array asociativo. Si los datos contienen archivos, también se procesan y se almacenan en la variable global `$_FILES`.
     * 
     * @return array Un array asociativo que contiene los datos procesados.
     * 
     * @throws Exception Si ocurre un error durante el procesamiento de los datos.
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
