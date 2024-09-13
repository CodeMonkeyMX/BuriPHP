<?php

/**
 * @package BuriPHP.Libraries
 *
 * @since 2.0Alpha
 * @version 1.3
 * @license You can see LICENSE.txt
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperConvert;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperServer;
use Libraries\BuriPHP\Helpers\HelperString;
use Libraries\BuriPHP\Helpers\HelperValidate;

class Router
{
    private array $urls = [];
    private int $useVersion = 1;
    private string $currentEndpoint = "";
    private string $useModule = "";
    private string $useController = "";

    /**
     * Inicializa los endpoints globales.
     */
    final public function __construct()
    {
        if (!isset($GLOBALS['_APP']['ENDPOINTS'])) {
            $GLOBALS['_APP']['ENDPOINTS'] = HelperConvert::toArray("");
        }

        $this->reset();
    }

    /**
     * Reinicia las variables.
     */
    private function reset()
    {
        $this->useVersion = 1;
        $this->currentEndpoint = "";
        $this->useModule = "";
        $this->useController = "";
    }

    /**
     * Genera un enpoint vacío.
     * 
     * @return array
     */
    public function endpoints()
    {
        return HelperConvert::toArray([]);
    }

    /**
     * Asigna la version que se usará para el endpoint.
     * 
     * @param int $int
     * 
     * @return mixed
     */
    final public function useVersion($int)
    {
        $this->useVersion = (int) $int;

        return $this;
    }

    /**
     * Crea un grupo de endpoint.
     * 
     * @param string $str
     * 
     * @return mixed
     */
    final public function addGroup($str)
    {
        if (HelperValidate::existWord($str, '__VERSION__')) {
            $this->currentEndpoint = HelperString::replaceAll($str, '__VERSION__', 'v' . $this->useVersion);
        } else {
            $this->currentEndpoint = $str;
        }

        return $this;
    }

    /**
     * Asigna el modulo.
     * 
     * @param string $str
     * 
     * @return mixed
     */
    final public function useModule($str)
    {
        $this->useModule = $str;

        return $this;
    }

    /**
     * Asigna el controlador.
     * 
     * @param string $str
     * 
     * @return mixed
     */
    final public function useController($str)
    {
        $this->useController = $str;

        return $this;
    }

    /**
     * Asigna usar el metodo GET
     * 
     * @param string $str
     * @param string $method
     * @param string $contentType
     * 
     * @return mixed
     */
    final public function get($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'GET', $method, $settings);

        return $this;
    }

    /**
     * Asigna usar el metodo POST
     * 
     * @param string $str
     * @param string $method
     * @param string $contentType
     * 
     * @return mixed
     */
    final public function post($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'POST', $method, $settings);

        return $this;
    }

    /**
     * Asigna usar el metodo PUT
     * 
     * @param string $str
     * @param string $method
     * @param string $contentType
     * 
     * @return mixed
     */
    final public function put($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'PUT', $method, $settings);

        return $this;
    }

    /**
     * Asigna usar el metodo PATCH
     * 
     * @param string $str
     * @param string $method
     * @param string $contentType
     * 
     * @return mixed
     */
    final public function patch($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'PATCH', $method, $settings);

        return $this;
    }

    /**
     * Asigna usar el metodo DELETE
     * 
     * @param string $str
     * @param string $method
     * @param string $contentType
     * 
     * @return mixed
     */
    final public function delete($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'DELETE', $method, $settings);

        return $this;
    }

    /**
     * Crea un string con todos los parametros del endpoint.
     * 
     * @param string $url
     * @param string $requestMethod
     * @param string $method
     * @param string $contentType
     */
    private function join($url, $requestMethod, $method, $settings)
    {
        $url = explode('/', $url);
        $url = HelperArray::compact($url);

        $arrParams = [];

        foreach ($url as $key => $value) {
            if (
                HelperString::getLeftNum($value, 1) === '{' &&
                HelperString::getRightNum($value, 1) === '}'
            ) {
                $arrParams[] = [
                    $key,
                    HelperString::getBetween($value, '{', '}')
                ];
            }
        }

        if (get_called_class() != 'Libraries\Endpoints\Endpoints') {
            $this->useModule(explode('\\', get_called_class())[0]);
        }

        $arrParams = json_encode($arrParams);
        $url = HelperArray::joinValues($url, '/');

        $ContentType = HelperArray::getValueByKey($settings, 'ContentType');

        if (!HelperValidate::isEmpty($ContentType)) {
            $settings = HelperArray::removeKey($settings, 'ContentType');
        }

        $settingsImplode = "";

        foreach ($settings as $key => $value) {
            $settingsImplode .= $key . '=' . $value . ',';
        }

        if (HelperValidate::isEmpty($settingsImplode)) {
            $settingsImplode = "[]";
        }

        $this->urls = HelperArray::append(
            $this->urls,
            "$requestMethod:/$url:$this->useModule:$this->useController:$method:$arrParams:$ContentType:$settingsImplode"
        );
    }

    /**
     * Establece los endpoints
     */
    final public function assign()
    {
        $GLOBALS['_APP']['ENDPOINTS'] = HelperArray::combine($GLOBALS['_APP']['ENDPOINTS'], $this->urls);
        $GLOBALS['_APP']['ENDPOINTS'] = HelperArray::removeDuplicates($GLOBALS['_APP']['ENDPOINTS']);

        $this->reset();
    }

    /**
     * Agrega los endpoints desde un módulo.
     * 
     * @param string $module
     */
    final public function addForModule($module)
    {
        try {
            if (HelperFile::exists(PATH_MODULES . $module . DS . 'Endpoints' . CLASS_PHP)) {
                include_once(PATH_MODULES . $module . DS . 'Endpoints' . CLASS_PHP);

                $namespaceModuleEndpoints = '\\' . $module . '\Endpoints\Endpoints';

                (new $namespaceModuleEndpoints())->endpoints();
            } else {
                throw new \Exception("No sé encontró Endpoints.class.php en el módulo $module");
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Divide el endpoint en parametros.
     * 
     * @param string $endpoint
     */
    final public static function explodeEndpoint($endpoint)
    {
        if (HelperValidate::isEmpty($endpoint)) {
            return [];
        }

        $endpoint = explode(':', $endpoint);

        $params = [];
        $currentUri = HelperServer::getCurrentPathInfo();

        foreach (json_decode($endpoint[5]) as $value) {
            $params[$value[1]] = $currentUri[$value[0]];
        }

        $settingsArr = [];

        if (is_array(explode(',', $endpoint[7]))) {
            $settings = HelperArray::compact(explode(',', $endpoint[7]));

            foreach ($settings as $value) {
                $arr = explode('=', $value);

                if (isset($arr[0]) && isset($arr[1])) {
                    $settingsArr[$arr[0]] = $arr[1];
                }
            }
        }

        return [
            'REQUEST_METHOD' => $endpoint[0],
            'REQUEST_URI' => $endpoint[1],
            'MODULE' => $endpoint[2],
            'CONTROLLER' => $endpoint[3],
            'METHOD' => $endpoint[4],
            'PARAMS' => $params,
            'CONTENT_TYPE' => $endpoint[6],
            'SETTINGS' => $settingsArr
        ];
    }

    /**
     * Devuelve el endpoint actual.
     */
    final public static function getEndpoint()
    {
        return [
            $GLOBALS['_APP']['ENDPOINT'],
            self::explodeEndpoint($GLOBALS['_APP']['ENDPOINT'])
        ];
    }
}
