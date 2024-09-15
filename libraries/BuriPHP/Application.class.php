<?php

namespace Libraries\BuriPHP;

use BuriPHP\Settings;
use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperConvert;
use Libraries\BuriPHP\Helpers\HelperDate;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperHeader;
use Libraries\BuriPHP\Helpers\HelperLog;
use Libraries\BuriPHP\Helpers\HelperServer;
use Libraries\BuriPHP\Helpers\HelperValidate;
use Libraries\Endpoints\Endpoints;

/**
 * Clase final Application
 *
 * Esta clase representa la aplicación principal de BuriPHP.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.5
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @final
 */
final class Application
{
    /**
     * Constructor de la clase Application.
     *
     * Este constructor inicializa la configuración de la aplicación estableciendo
     * el nivel de reporte de errores y configurando la zona horaria local.
     *
     * @return void
     */
    public function __construct()
    {
        HelperServer::errorReporting(Settings::$errorReporting);
        HelperDate::setLocateTimeZone();
    }

    /**
     * Ejecuta el flujo principal de la aplicación.
     *
     * Este método realiza las siguientes acciones:
     * 1. Ejecuta todos los endpoints configurados.
     * 2. Traza el endpoint actual.
     * 3. Verifica si el endpoint está vacío y lanza un error 404 si es necesario.
     * 4. Verifica si el REQUEST_METHOD está permitido y lanza un error 405 si no lo está.
     * 5. Verifica la validez del módulo, controlador y método en el endpoint.
     * 6. Verifica la existencia del módulo y del controlador.
     * 7. Inicializa la clase del controlador si existe.
     * 8. Verifica la existencia del método en el controlador y lo ejecuta si existe.
     * 9. Maneja excepciones y errores durante la ejecución.
     *
     * @throws \Exception Si ocurre algún error durante la ejecución.
     * @return bool False si ocurre algún error que impida la ejecución.
     */
    public function exec()
    {
        /**
         * Manda a ejecutar todos los endpoints.
         */
        $this->triggerEndpoints();

        /**
         * Traza el endpoint actual.
         */
        $this->traceEndpoint();
        $trace = Router::explodeEndpoint($GLOBALS['_APP']['ENDPOINT']);

        /**
         * Verifica si el endpoint esta vacío.
         * Si esta vacío, lanza un error 404.
         */
        if (
            HelperValidate::isEmpty($GLOBALS['_APP']['ENDPOINT']) &&
            HelperValidate::isEmpty($GLOBALS['_APP']['ALLOWED_METHODS'])
        ) {
            HelperHeader::setStatusCode(404);

            return false;
        }

        /**
         * Verifica si esta permitido el REQUEST_METHOD.
         */
        if (
            true !== HelperArray::existsValue(
                $GLOBALS['_APP']['ALLOWED_METHODS'],
                HelperServer::getValue('REQUEST_METHOD')
            ) && HelperServer::getValue('REQUEST_METHOD') !== 'OPTIONS'
        ) {
            HelperHeader::setContentType('json');
            HelperHeader::setStatusCode(405);

            echo json_encode([
                'status' => 405,
                'message' => 'Method Not Allowed'
            ], JSON_PRETTY_PRINT);

            return false;
        }

        try {
            /**
             * Verifica el nombre del módulo en el endpoint.
             */
            if (HelperValidate::isEmpty($trace['MODULE'])) {
                $exceptionMsg = "No se estableció el module para el endpoint: " . implode(':', $trace);

                HelperHeader::setStatusCode(500);
                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            }

            /**
             * Verifica el nombre del controlador en el endpoint.
             */
            if (HelperValidate::isEmpty($trace['CONTROLLER'])) {
                $exceptionMsg = "No se estableció el controller para el endpoint: " . implode(':', $trace);

                HelperHeader::setStatusCode(500);
                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            }

            /**
             * Verifica el nombre del método en el endpoint.
             */
            if (HelperValidate::isEmpty($trace['METHOD'])) {
                $exceptionMsg = "No se estableció el method para el endpoint: " . implode(':', $trace);

                HelperHeader::setStatusCode(500);
                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            }

            /**
             * Verifica que exista el módulo.
             */
            if (!HelperValidate::isDir(PATH_MODULES . $trace['MODULE'])) {
                $exceptionMsg = "No existe el module: " . PATH_MODULES . $trace['MODULE'];

                HelperHeader::setStatusCode(500);
                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            }

            /**
             * Verifica que exista una clase con nombre del controlador.
             */
            if (HelperFile::exists(PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CLASS_PHP)) {
                require_once PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CLASS_PHP;
            }

            /**
             * Verifica que exista el controlador.
             */
            if (!HelperFile::exists(PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CONTROLLER_PHP)) {
                $exceptionMsg = "No existe el controller: " . PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CONTROLLER_PHP;

                HelperHeader::setStatusCode(500);
                if (json_decode(json_encode(getallheaders()), true)['Sec-Fetch-Mode'] !== 'cors') {
                    HelperLog::saveError($exceptionMsg);
                }
                throw new \Exception($exceptionMsg);
            } else {
                define('MODULE_NAME', $trace['MODULE']);
                define('MODULE_ROOT', PATH_MODULES . $trace['MODULE'] . DS);

                require_once PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CONTROLLER_PHP;
            }

            /**
             * Verifica que exista la clase del controlador.
             * Si existe, la inicializa.
             */
            $namespace = '\Controllers\\' . $trace['CONTROLLER'];

            if (!class_exists($namespace)) {
                $exceptionMsg = "No existe la class: " . $namespace . " en: " . PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CONTROLLER_PHP;

                HelperHeader::setStatusCode(500);
                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            } else {
                HelperHeader::setContentType($trace['CONTENT_TYPE']);

                if (class_exists('\\Libraries\Build\Build')) {
                    $build = new \Libraries\Build\Build($trace);

                    if (method_exists($build, 'startup')) {
                        if (false === $build->startup()) {
                            return false;
                        }
                    }
                }

                $controller = new $namespace();
            }

            /**
             * Verifica que exista el método.
             * Si existe, ejecuta el método.
             */
            if (!method_exists($controller, $trace['METHOD'])) {
                $exceptionMsg = "No existe el method: " . $trace['METHOD'] . "() en: " . PATH_MODULES . $trace['MODULE'] . DS . $trace['CONTROLLER'] . CONTROLLER_PHP;

                HelperHeader::setStatusCode(500);
                HelperLog::saveError($exceptionMsg);
                throw new \Exception($exceptionMsg);
            } else {
                $app = $controller->{$trace['METHOD']}();

                if (is_array($app)) {
                    echo json_encode($app, JSON_PRETTY_PRINT);
                } else if (is_string($app)) {
                    echo $app;
                }
            }
        } catch (\Throwable $th) {
            throw $th;

            return false;
        }
    }

    /**
     * Activa los endpoints definidos en la clase Endpoints.
     *
     * Este método crea una nueva instancia de la clase Endpoints y llama al método endpoints() 
     * para activar los endpoints configurados.
     *
     * @return void
     */
    private function triggerEndpoints(): void
    {
        (new Endpoints())->endpoints();
    }

    /**
     * Método privado traceEndpoint
     *
     * Este método se encarga de rastrear y determinar el endpoint actual y los métodos permitidos
     * basándose en la información de la solicitud actual y la configuración de endpoints globales.
     *
     * Funcionalidad:
     * - Inicializa las variables $endpoint y $allowedMethods.
     * - Verifica si la ruta actual comienza con '/'.
     * - Recorre los endpoints globales y compara la ruta actual con los endpoints configurados.
     * - Si encuentra una coincidencia, actualiza $allowedMethods y $endpoint según el método de solicitud.
     * - Si la ruta actual no comienza con '/', realiza una comparación más detallada de la URI.
     * - Actualiza las variables globales '_APP' con el endpoint actual y los métodos permitidos.
     *
     * Variables Globales:
     * - $GLOBALS['_APP']['ENDPOINTS']: Lista de endpoints configurados.
     * - $GLOBALS['_APP']['ENDPOINT']: Endpoint actual determinado.
     * - $GLOBALS['_APP']['ALLOWED_METHODS']: Métodos permitidos para el endpoint actual.
     *
     * Dependencias:
     * - HelperConvert::toArray
     * - HelperServer::getCurrentPathInfo
     * - HelperServer::getValue
     * - HelperArray::append
     * - HelperArray::compact
     * - HelperValidate::isEmpty
     * - HelperArray::dif
     */
    private function traceEndpoint()
    {
        $endpoint = (string) "";
        $allowedMethods = HelperConvert::toArray("");

        if (HelperServer::getCurrentPathInfo()[0] == '/') {
            foreach ($GLOBALS['_APP']['ENDPOINTS'] as $key => $value) {
                $arrEndpoint = explode(':', $value);

                if ($arrEndpoint[1] == '/') {
                    $allowedMethods = HelperArray::append($allowedMethods, $arrEndpoint[0]);

                    if (HelperServer::getValue('REQUEST_METHOD') === $arrEndpoint[0]) {
                        $endpoint = $GLOBALS['_APP']['ENDPOINTS'][$key];
                    }
                }
            }
        } else {
            foreach ($GLOBALS['_APP']['ENDPOINTS'] as $key => $value) {
                $currentUri = HelperServer::getCurrentPathInfo();
                $arrEndpoint = explode(':', $value);
                $arrEndpoint[1] = HelperArray::compact(explode('/', $arrEndpoint[1]));

                if (count($currentUri) == count($arrEndpoint[1])) {
                    foreach (json_decode($arrEndpoint[5], true) as $_value) {
                        $currentUri[$_value[0]] = "{" . $_value[1] . "}";
                    }

                    if (HelperValidate::isEmpty(HelperArray::dif($currentUri, $arrEndpoint[1]))) {
                        $allowedMethods = HelperArray::append($allowedMethods, $arrEndpoint[0]);

                        if (HelperServer::getValue('REQUEST_METHOD') === $arrEndpoint[0]) {
                            $endpoint = $GLOBALS['_APP']['ENDPOINTS'][$key];
                        }
                    }
                }
            }
        }

        $GLOBALS['_APP']['ENDPOINT'] = $endpoint;
        $GLOBALS['_APP']['ALLOWED_METHODS'] = $allowedMethods;
    }

    /**
     * Establece la configuración de la aplicación.
     *
     * @param array $args Un array asociativo con las siguientes claves opcionales:
     *  - 'domain': El dominio de la aplicación.
     *  - 'lang': El idioma predeterminado de la aplicación.
     *  - 'timeZone': La zona horaria de la aplicación.
     *  - 'locale': La configuración regional de la aplicación.
     *  - 'errorReporting': El nivel de reporte de errores.
     *  - 'secret': La clave secreta de la aplicación.
     *  - 'useDatabase': Booleano que indica si se debe usar la base de datos.
     *  - 'dbType': El tipo de base de datos.
     *  - 'dbHost': El host de la base de datos.
     *  - 'dbName': El nombre de la base de datos.
     *  - 'dbUser': El usuario de la base de datos.
     *  - 'dbPass': La contraseña de la base de datos.
     *  - 'dbCharset': El conjunto de caracteres de la base de datos.
     *  - 'dbPrefix': El prefijo de las tablas de la base de datos.
     *  - 'dbPort': El puerto de la base de datos.
     *
     * @return void
     */
    public static function setSettings(...$args)
    {
        $path = PATH_ROOT . DS . 'Settings.php';

        $file = HelperFile::openText($path, 'r+');
        $content = HelperFile::readChars($file, HelperFile::getSizeBytes($path));
        HelperFile::close($file);

        $content = explode(PHP_EOL, $content);

        if (isset($args['domain']) || !empty($args['domain'])) {
            $content[(28 - 1)] = "\tpublic static \$domain = '" . $args['domain'] . "';";
        } else {
            $content[(28 - 1)] = "\tpublic static \$domain = '" . HelperServer::getDomain() . "';";
        }

        if (isset($args['lang']) || !empty($args['lang'])) {
            $content[(38 - 1)] = "\tpublic static \$langDefault = '" . $args['lang'] . "';";
        }

        if (isset($args['timeZone']) || !empty($args['timeZone'])) {
            $content[(47 - 1)] = "\tpublic static \$timeZone = '" . $args['timeZone'] . "';";
        }

        if (isset($args['locale']) || !empty($args['locale'])) {
            $content[(56 - 1)] = "\tpublic static \$locale = '" . $args['locale'] . "';";
        }

        if (isset($args['errorReporting']) || !empty($args['errorReporting'])) {
            $content[(66 - 1)] = "\tpublic static \$errorReporting = '" . $args['errorReporting'] . "';";
        }

        if (isset($args['secret']) || !empty($args['secret'])) {
            $content[(75 - 1)] = "\tpublic static \$secret = '" . $args['secret'] . "';";
        }

        if (isset($args['useDatabase']) && $args['useDatabase'] === true) {
            $content[(84 - 1)] = "\tpublic static \$useDatabase = true;";
        }

        if (isset($args['useDatabase']) && $args['useDatabase'] === false) {
            $content[(84 - 1)] = "\tpublic static \$useDatabase = false;";
        }

        if (isset($args['dbType']) || !empty($args['dbType'])) {
            $content[(94 - 1)] = "\tpublic static \$dbType = '" . $args['dbType'] . "';";
        }

        if (isset($args['dbHost']) || !empty($args['dbHost'])) {
            $content[(103 - 1)] = "\tpublic static \$dbHost = '" . $args['dbHost'] . "';";
        }

        if (isset($args['dbName']) || !empty($args['dbName'])) {
            $content[(112 - 1)] = "\tpublic static \$dbName = '" . $args['dbName'] . "';";
        }

        if (isset($args['dbUser']) || !empty($args['dbUser'])) {
            $content[(121 - 1)] = "\tpublic static \$dbUser = '" . $args['dbUser'] . "';";
        }

        if (isset($args['dbPass']) || !empty($args['dbPass'])) {
            $content[(130 - 1)] = "\tpublic static \$dbPass = '" . $args['dbPass'] . "';";
        }

        if (isset($args['dbCharset']) || !empty($args['dbCharset'])) {
            $content[(139 - 1)] = "\tpublic static \$dbCharset = '" . $args['dbCharset'] . "';";
        }

        if (isset($args['dbPrefix']) || !empty($args['dbPrefix'])) {
            $content[(148 - 1)] = "\tpublic static \$dbPrefix = '" . $args['dbPrefix'] . "';";
        }

        if (isset($args['dbPort']) || !empty($args['dbPort'])) {
            $content[(157 - 1)] = "\tpublic static \$dbPort = " . $args['dbPort'] . ";";
        }

        $file = HelperFile::openText($path, 'w');
        HelperFile::write($file, implode(PHP_EOL, $content));
        HelperFile::close($file);
    }
}
