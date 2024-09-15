<?php

namespace BuriPHP;

/**
 * Clase Settings
 * 
 * Esta clase extiende de AppSettings y se utiliza para almacenar diversas configuraciones de la aplicación.
 * 
 * Propiedades estáticas:
 * 
 * - $domain: Dominio del sitio web. Ejemplo: 'example.com'.
 * - $langDefault: Idioma predeterminado para la aplicación. Ejemplo: 'es', 'en', 'fr', 'ru'.
 * - $timeZone: Configuración de la zona horaria. Ejemplo: 'America/Mexico_City'.
 * - $locale: Configuración de la localización. Ejemplo: 'es_MX.UTF-8'.
 * - $errorReporting: Configuración del entorno de reporte de errores. Ejemplo: 'development', 'production'.
 * - $secret: Valor secreto para configuraciones internas.
 * - $useDatabase: Indica si se debe utilizar una base de datos.
 * - $dbType: Tipo de base de datos utilizada. Ejemplo: 'MySQL', 'MariaDB', 'MSSQL', 'PgSQL', 'Oracle', 'Sybase'.
 * - $dbHost: Dirección del host de la base de datos.
 * - $dbName: Nombre de la base de datos a utilizar.
 * - $dbUser: Nombre de usuario para la conexión a la base de datos.
 * - $dbPass: Contraseña para la conexión a la base de datos.
 * - $dbCharset: Juego de caracteres de la base de datos. Ejemplo: 'utf8'.
 * - $dbPrefix: Prefijo de la base de datos, opcional.
 * - $dbPort: Puerto de la base de datos. Ejemplo: 3306.
 * 
 * @package Application
 * @author Kiske
 * @since 1.0.0
 * @version 1.1.0
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Settings extends AppSettings
{
    /**
     * Dominio del sitio web. Esta variable se utiliza para almacenar el nombre de dominio del sitio web.
     * 
     * @static
     * 
     * @var string $domain
     * 
     * @example 'example.com'
     */
    public static $domain = '';

    /**
     * Idioma predeterminado para la aplicación.
     * 
     * @static
     *
     * @var string $langDefault El idioma predeterminado, configurado como 'es' (español).
     * 
     * @example es, en, fr, ru..
     */
    public static $langDefault = 'es';

    /**
     * Configuración de la zona horaria.
     *
     * Esta propiedad estática define la zona horaria utilizada por la aplicación.
     * En este caso, está configurada para 'America/Mexico_City'.
     * 
     * @static
     *
     * @var string $timeZone Zona horaria de la aplicación.
     */
    public static $timeZone = 'America/Mexico_City';

    /**
     * Configuración de la localización.
     * 
     * @static
     *
     * @var string $locale La configuración regional utilizada por la aplicación. 
     *                     En este caso, está configurada para 'es_MX.UTF-8', 
     *                     que corresponde al español de México con codificación UTF-8.
     */
    public static $locale = 'es_MX.UTF-8';

    /**
     * Configuración del entorno de reporte de errores.
     * 
     * @static
     *
     * @var string $errorReporting Define el nivel de reporte de errores. 
     *                             Puede ser 'development' para mostrar todos los errores 
     *                             o 'production' para ocultar errores no críticos.
     * 
     * @example default, none, simple, maximum, development
     */
    public static $errorReporting = 'development';

    /**
     * Esta variable almacena un valor secreto que se utiliza para configuraciones internas.
     *
     * @static
     *
     * @var string $secret Valor secreto para configuraciones internas.
     */
    public static $secret = '';

    /**
     * Indica si se debe utilizar una base de datos.
     * 
     * @static
     *
     * @var bool $useDatabase Indica si se debe utilizar una base de datos.
     */
    public static $useDatabase = false;

    /**
     * Tipo de base de datos utilizada.
     * 
     * @static
     *
     * @var string $dbType Tipo de base de datos, por defecto 'MariaDB'.
     * 
     * @example MySQL, MariaDB, MSSQL, PgSQL, Oracle, Sybase
     */
    public static $dbType = 'MariaDB';

    /**
     * Esta variable almacena la dirección del host de la base de datos.
     *
     * @static
     *
     * @var string $dbHost Dirección del host de la base de datos.
     */
    public static $dbHost = '';

    /**
     * Nombre de la base de datos a utilizar.
     * 
     * @static
     *
     * @var string $dbName Nombre de la base de datos a utilizar.
     */
    public static $dbName = '';

    /**
     * Nombre de usuario para la conexión a la base de datos.
     *
     * @static
     *
     * @var string $dbUser Nombre de usuario para la conexión a la base de datos.
     */
    public static $dbUser = '';

    /**
     * Contraseña para la conexión a la base de datos.
     *
     * @static
     *
     * @var string $dbPass Contraseña para la conexión a la base de datos.
     */
    public static $dbPass = '';

    /**
     * Juego de caracteres de la base de datos.
     *
     * @static
     *
     * @var string $dbCharset Juego de caracteres de la base de datos.
     */
    public static $dbCharset = 'utf8';

    /**
     * Prefijo de la base de datos, opcional.
     *
     * @static
     *
     * @var string $dbPrefix Prefijo de la base de datos.
     */
    public static $dbPrefix = '';

    /**
     * Puerto de la base de datos.
     *
     * @static
     *
     * @var integer $dbPort Puerto de la base de datos.
     */
    public static $dbPort = 3306;
}
