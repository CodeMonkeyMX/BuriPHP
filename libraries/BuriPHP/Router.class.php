<?php

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperConvert;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperServer;
use Libraries\BuriPHP\Helpers\HelperString;
use Libraries\BuriPHP\Helpers\HelperValidate;

/**
 * Clase Router
 * 
 * Esta clase se encarga de manejar las rutas dentro de la aplicación.
 * Proporciona métodos para definir y gestionar las rutas, así como para 
 * despachar las solicitudes a los controladores correspondientes.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.4
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Router
{
    /**
     * @var array $urls Arreglo que almacena las URLs registradas en el enrutador.
     */
    private array $urls = [];

    /**
     * @var int $useVersion La versión del endpoint que se utilizará. Por defecto es 1.
     */
    private int $useVersion = 1;

    /**
     * @var string $currentEndpoint
     * 
     * Almacena el endpoint actual que se está procesando.
     * Inicialmente se establece como una cadena vacía.
     */
    private string $currentEndpoint = "";

    /**
     * @var string $useModule
     * 
     * Esta propiedad almacena el nombre del módulo que se está utilizando.
     * Inicialmente, se establece como una cadena vacía.
     */
    private string $useModule = "";

    /**
     * @var string $useController
     * 
     * Esta propiedad almacena el nombre del controlador que se utilizará.
     * Inicialmente está vacía.
     */
    private string $useController = "";

    /**
     * Constructor de la clase Router.
     *
     * Este método se ejecuta al instanciar la clase Router. Verifica si la variable global
     * '_APP' contiene la clave 'ENDPOINTS'. Si no está definida, la inicializa como un array vacío.
     * Luego, llama al método reset() para restablecer el estado del objeto.
     *
     * @return void
     */
    final public function __construct()
    {
        if (!isset($GLOBALS['_APP']['ENDPOINTS'])) {
            $GLOBALS['_APP']['ENDPOINTS'] = HelperConvert::toArray("");
        }

        $this->reset();
    }

    /**
     * Restablece las propiedades del enrutador a sus valores predeterminados.
     *
     * Este método establece la versión de uso a 1, y reinicia las propiedades
     * `currentEndpoint`, `useModule` y `useController` a cadenas vacías.
     *
     * @return void
     */
    private function reset()
    {
        $this->useVersion = 1;
        $this->currentEndpoint = "";
        $this->useModule = "";
        $this->useController = "";
    }

    /**
     * Devuelve un array de endpoints.
     *
     * @return array Un array vacío convertido utilizando HelperConvert::toArray().
     */
    public function endpoints()
    {
        return HelperConvert::toArray([]);
    }

    /**
     * Establece la versión del endpoint a utilizar.
     *
     * @param int $int La versión que se va a establecer.
     * @return self Retorna la instancia actual para permitir el encadenamiento de métodos.
     */
    final public function useVersion($int)
    {
        $this->useVersion = (int) $int;

        return $this;
    }

    /**
     * Añade un grupo de rutas al enrutador.
     *
     * Este método permite agregar un grupo de rutas al enrutador. Si la cadena
     * proporcionada contiene la palabra '__VERSION__', esta será reemplazada por
     * la versión actual en uso.
     *
     * @param string $str La cadena que representa el grupo de rutas. Puede contener
     *                    la palabra '__VERSION__' que será reemplazada por la versión
     *                    actual.
     * @return self Retorna la instancia actual del enrutador para permitir el encadenamiento
     *              de métodos.
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
     * Establece el módulo a utilizar.
     *
     * @param string $str El nombre del módulo a utilizar.
     * @return self Retorna la instancia actual para permitir el encadenamiento de métodos.
     */
    final public function useModule($str)
    {
        $this->useModule = $str;

        return $this;
    }

    /**
     * Establece el controlador a utilizar.
     *
     * @param string $str Nombre del controlador.
     * @return self Retorna la instancia actual para permitir el encadenamiento de métodos.
     */
    final public function useController($str)
    {
        $this->useController = $str;

        return $this;
    }

    /**
     * Registra una nueva ruta GET en el enrutador.
     *
     * @param string $str La ruta que se añadirá al endpoint actual.
     * @param string|callable $method El método o controlador que se ejecutará cuando se acceda a la ruta.
     * @param array $settings Configuraciones adicionales para la ruta.
     * @return self Retorna la instancia actual del enrutador para permitir el encadenamiento de métodos.
     */
    final public function get($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'GET', $method, $settings);

        return $this;
    }

    /**
     * Registra una nueva ruta HTTP POST en el enrutador.
     *
     * @param string $str La ruta relativa que se añadirá al endpoint actual.
     * @param string|callable $method El método o función que se ejecutará cuando se acceda a la ruta.
     * @param array $settings (Opcional) Configuraciones adicionales para la ruta.
     * @return self Retorna la instancia actual del enrutador para permitir el encadenamiento de métodos.
     */
    final public function post($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'POST', $method, $settings);

        return $this;
    }

    /**
     * Registra una ruta con el método HTTP PUT.
     *
     * @param string $str La ruta a la que se asociará el método.
     * @param callable|string $method El método que se ejecutará cuando se acceda a la ruta.
     * @param array $settings Configuraciones adicionales para la ruta.
     * @return self Retorna la instancia actual para permitir el encadenamiento de métodos.
     */
    final public function put($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'PUT', $method, $settings);

        return $this;
    }

    /**
     * Registra una ruta PATCH en el enrutador.
     *
     * @param string $str La cadena que representa la ruta.
     * @param callable|string $method El método o controlador que manejará la solicitud PATCH.
     * @param array $settings (Opcional) Configuraciones adicionales para la ruta.
     * @return self Retorna la instancia del enrutador para permitir el encadenamiento de métodos.
     */
    final public function patch($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'PATCH', $method, $settings);

        return $this;
    }

    /**
     * Registra una nueva ruta DELETE en el enrutador.
     *
     * @param string $str La cadena que representa la ruta.
     * @param string $method El método que se llamará cuando se acceda a la ruta.
     * @param array $settings (Opcional) Configuraciones adicionales para la ruta.
     * @return self Retorna la instancia actual del enrutador.
     */
    final public function delete($str, $method, array $settings = [])
    {
        $this->join($this->currentEndpoint . $str, 'DELETE', $method, $settings);

        return $this;
    }

    /**
     * Une una URL con el método de solicitud, el método de la clase y la configuración proporcionada.
     *
     * @param string $url La URL a la que se unirá.
     * @param string $requestMethod El método de solicitud HTTP (GET, POST, etc.).
     * @param string $method El método de la clase que se llamará.
     * @param array $settings Configuración adicional para la solicitud.
     *
     * @return void
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
     * Asigna las URLs al arreglo global de endpoints y elimina duplicados.
     *
     * Este método combina las URLs actuales con las existentes en el arreglo global
     * de endpoints y luego elimina cualquier duplicado. Finalmente, resetea el estado
     * interno del objeto.
     *
     * @return void
     */
    final public function assign()
    {
        $GLOBALS['_APP']['ENDPOINTS'] = HelperArray::combine($GLOBALS['_APP']['ENDPOINTS'], $this->urls);
        $GLOBALS['_APP']['ENDPOINTS'] = HelperArray::removeDuplicates($GLOBALS['_APP']['ENDPOINTS']);

        $this->reset();
    }

    /**
     * Agrega los endpoints para un módulo específico.
     *
     * Este método verifica si el archivo `Endpoints.class.php` existe en el directorio del módulo especificado.
     * Si el archivo existe, lo incluye y llama al método `endpoints` de la clase `Endpoints` dentro del espacio de nombres del módulo.
     * Si el archivo no existe, lanza una excepción.
     *
     * @param string $module El nombre del módulo para el cual se agregarán los endpoints.
     * @throws \Exception Si no se encuentra el archivo `Endpoints.class.php` en el módulo especificado.
     * @throws \Throwable Si ocurre cualquier otro error durante la ejecución.
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
     * Descompone un endpoint en sus componentes y devuelve un array asociativo con la información.
     *
     * @param string $endpoint El endpoint a descomponer.
     * 
     * @return array Un array asociativo con los siguientes elementos:
     * - 'REQUEST_METHOD': El método de la solicitud (GET, POST, etc.).
     * - 'REQUEST_URI': La URI de la solicitud.
     * - 'MODULE': El módulo al que pertenece el endpoint.
     * - 'CONTROLLER': El controlador que maneja el endpoint.
     * - 'METHOD': El método del controlador que maneja el endpoint.
     * - 'PARAMS': Un array asociativo de parámetros extraídos de la URI actual.
     * - 'CONTENT_TYPE': El tipo de contenido de la solicitud.
     * - 'SETTINGS': Un array asociativo de configuraciones adicionales.
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
     * Obtiene el endpoint actual.
     *
     * @return array Un arreglo que contiene el endpoint actual y su versión explotada.
     */
    final public static function getEndpoint()
    {
        return [
            $GLOBALS['_APP']['ENDPOINT'],
            self::explodeEndpoint($GLOBALS['_APP']['ENDPOINT'])
        ];
    }
}
