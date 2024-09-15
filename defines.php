<?php

/**
 * Definiciones de constantes para la configuración del proyecto.
 *
 * Este archivo define varias constantes que se utilizan en todo el proyecto
 * para facilitar la gestión de rutas y extensiones de archivos.
 *
 * Constantes definidas:
 * - CLASS_PHP: Extensión para archivos de clase.
 * - CONTROLLER_PHP: Extensión para archivos de controlador.
 * - MODEL_PHP: Extensión para archivos de modelo.
 * - SERVICE_PHP: Extensión para archivos de servicio.
 * - REPOSITORY_PHP: Extensión para archivos de repositorio.
 * - INTERFACE_PHP: Extensión para archivos de interfaz.
 * - PATH_ROOT: Ruta raíz del proyecto.
 * - DS: Separador de directorios del sistema.
 * - PATH_SETTINGS: Ruta al archivo de configuración principal.
 * - PATH_LIBRARIES: Ruta al directorio de bibliotecas.
 * - PATH_BURIPHP_LIBRARIES: Ruta al directorio de bibliotecas específicas de BuriPHP.
 * - PATH_BURIPHP_HELPERS: Ruta al directorio de ayudantes específicos de BuriPHP.
 * - PATH_MODULES: Ruta al directorio de módulos.
 * - PATH_SHARED: Ruta al directorio de recursos compartidos.
 * - PATH_LOGS: Ruta al directorio de logs.
 * 
 * @package Application
 * @author Kiske
 * @since 0.0.1
 * @version 1.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

/**
 * Define la constante CLASS_PHP que representa la extensión de archivo para las clases PHP.
 *
 * @constant string CLASS_PHP Extensión de archivo para las clases PHP.
 */
define('CLASS_PHP', '.class.php');

/**
 * Define una constante para la extensión de los archivos de controlador en PHP.
 * 
 * @const string CONTROLLER_PHP La extensión de los archivos de controlador.
 */
define('CONTROLLER_PHP', '.controller.php');

/**
 * Define una constante para la extensión de los archivos de modelo en PHP.
 * 
 * @const string MODEL_PHP La extensión de los archivos de modelo en PHP.
 */
define('MODEL_PHP', '.model.php');

/**
 * Define una constante para el sufijo de los archivos de servicio PHP.
 * 
 * @const string SERVICE_PHP Sufijo utilizado para identificar archivos de servicio PHP.
 */
define('SERVICE_PHP', '.service.php');

/**
 * Define una constante que representa el nombre del archivo de repositorio PHP.
 *
 * @const string REPOSITORY_PHP Nombre del archivo de repositorio PHP.
 */
define('REPOSITORY_PHP', '.repository.php');

/**
 * Define una constante que representa la extensión de archivo para interfaces PHP.
 * 
 * @const string INTERFACE_PHP La extensión de archivo para interfaces PHP.
 */
define('INTERFACE_PHP', '.interface.php');

/**
 * Define la constante PATH_ROOT con la ruta del directorio actual.
 * 
 * Esta constante se utiliza para establecer la ruta raíz del proyecto,
 * permitiendo que otras partes del código hagan referencia a esta ruta
 * de manera consistente.
 * 
 * @const string PATH_ROOT Ruta del directorio actual.
 */
define('PATH_ROOT', __DIR__);

/**
 * Define una constante 'DS' que representa el separador de directorios del sistema.
 * 
 * Esta constante se utiliza para asegurar la compatibilidad entre diferentes sistemas operativos
 * al trabajar con rutas de archivos y directorios.
 * 
 * @const string DS Separador de directorios del sistema.
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Define la constante PATH_SETTINGS que almacena la ruta completa al archivo 'Settings.php'.
 * 
 * @const string PATH_SETTINGS Ruta al archivo de configuración principal.
 */
define('PATH_SETTINGS', PATH_ROOT . DS . 'Settings.php');

/**
 * Define la constante PATH_LIBRARIES que representa la ruta a la carpeta 'libraries'.
 *
 * @const string PATH_LIBRARIES Ruta completa a la carpeta 'libraries'.
 */
define('PATH_LIBRARIES', PATH_ROOT . DS . 'libraries' . DS);

/**
 * Define la constante PATH_BURIPHP_LIBRARIES que representa la ruta a la carpeta de bibliotecas específicas de BuriPHP.
 *
 * @const string PATH_BURIPHP_LIBRARIES Ruta completa a la carpeta de bibliotecas específicas de BuriPHP.
 */
define('PATH_BURIPHP_LIBRARIES', PATH_LIBRARIES . 'BuriPHP' . DS);

/**
 * Define la constante PATH_BURIPHP_HELPERS que representa la ruta a la carpeta de ayudantes específicos de BuriPHP.
 *
 * @const string PATH_BURIPHP_HELPERS Ruta completa a la carpeta de ayudantes específicos de BuriPHP.
 */
define('PATH_BURIPHP_HELPERS', PATH_BURIPHP_LIBRARIES . 'Helpers' . DS);

/**
 * Define la constante PATH_LIBRARIES que representa la ruta a la carpeta de bibliotecas.
 *
 * @const string PATH_MODULES Ruta completa a la carpeta de módulos.
 */
define('PATH_MODULES', PATH_ROOT . DS . 'modules' . DS);

/**
 * Define la constante 'PATH_SHARED' que representa la ruta al directorio 'shared'.
 * 
 * @const string PATH_SHARED Ruta al directorio 'shared'.
 */
define('PATH_SHARED', PATH_ROOT . DS . 'shared' . DS);

/**
 * Define la constante PATH_LOGS que representa la ruta del directorio de logs.
 *
 * @const string PATH_LOGS Ruta completa del directorio de logs.
 */
define('PATH_LOGS', PATH_ROOT . DS . 'logs' . DS);
