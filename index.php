<?php

namespace BuriPHP;

/**
 * Inicializa la aplicación BuriPHP.
 * 
 * Este archivo realiza las siguientes tareas:
 * 
 * 1. Verifica la versión de PHP: Asegura que el servidor esté ejecutando PHP 8.0 o superior.
 * 2. Configura las cabeceras HTTP para permitir el acceso CORS desde cualquier origen.
 * 3. Incluye archivos de configuración y dependencias necesarias:
 *    - defines.php: Define constantes globales.
 *    - AppSettings.php: Configuraciones específicas de la aplicación.
 *    - Settings.php: Configuraciones generales.
 *    - autoload.php: Autocargador de Composer si existe.
 * 4. Registra una función de autocarga para cargar clases automáticamente:
 *    - Carga helpers y librerías específicas de BuriPHP.
 *    - Carga librerías externas.
 * 5. Inicia la aplicación BuriPHP llamando al método `exec` de la clase `Application`.
 * 
 * @package Application
 * @author David Miguel Gómez Macías (Kiske) < davidgomezmacias@gmail.com >
 * @since 0.0.1
 * @version 2.3
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * 
 * @throws Exception Si la versión de PHP es menor a 8.0.
 * 
 * @header Access-Control-Allow-Origin: *
 * @header Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization
 * @header Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE
 */
function runApp()
{
    if (version_compare(PHP_VERSION, '8.0', '<'))
        die('Your host needs to use PHP 8.0 or higher to run this version of BuriPHP.');

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE');

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'defines.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'AppSettings.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'Settings.php';

    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    spl_autoload_register(function ($str) {
        $arr = explode('\\', $str);
        $resource = end($arr);

        if (isset($arr[1]) && $arr[1] == 'BuriPHP') {
            if (in_array($arr[2], ['Helpers'])) { // Incluimos los helpers
                if (file_exists(PATH_BURIPHP_HELPERS . $resource . CLASS_PHP)) { // Helpers
                    require_once PATH_BURIPHP_HELPERS . $resource . CLASS_PHP;
                }
            } else {
                if (file_exists(PATH_BURIPHP_LIBRARIES . $resource . CLASS_PHP)) { // Librerias de BuriPHP
                    require_once PATH_BURIPHP_LIBRARIES . $resource . CLASS_PHP;
                }
            }
        } else {
            if (file_exists(PATH_LIBRARIES . $resource . CLASS_PHP)) { // Librerias externas
                require_once PATH_LIBRARIES . $resource . CLASS_PHP;
            }
        }
    });

    (new \Libraries\BuriPHP\Application())->exec();
}

runApp();
