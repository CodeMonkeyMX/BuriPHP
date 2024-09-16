<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperLog
 * 
 * Esta clase proporciona funcionalidades de registro de logs para la aplicación.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.2
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperLog
{
    /**
     * Guarda un rastro de log en un archivo.
     *
     * @param string $txt El texto del log que se va a guardar.
     * @param string $path (Opcional) La ruta donde se guardará el archivo de log. Si no se proporciona, se usará la constante PATH_LOGS.
     *
     * @throws \Exception Si no se puede crear el directorio especificado.
     * @throws \Throwable Si ocurre cualquier otro error durante la creación del directorio.
     *
     * @return bool Devuelve false si ocurre un error al crear el directorio.
     */
    public static function saveTrace($txt, $path = '')
    {
        $path = ($path === '') ? PATH_LOGS : $path . DS;

        if (!HelperValidate::isDir($path)) {
            try {
                if (!mkdir($path, 0700)) {
                    throw new \Exception('No se pudo crear el directorio' . ($path === '') ? ' de LOGS' : ': ' . $path);
                }
            } catch (\Throwable $th) {
                throw $th;

                return false;
            }
        }

        /* Ruta completa donde ubicar el archivo de logs */
        $fileLogs = $path . ($path === '' ?  'log-' : '') . date('Y-m-d') . '.log';

        $argNum  = func_num_args();
        $argList = func_get_args();

        for ($i = 1; $i < $argNum; $i++) {
            $txt = HelperString::replaceFirstOccurrence($txt, "%$i", $argList[$i]);
        }

        error_log(date('[Y-m-d H:i e] ') . $txt . PHP_EOL, 3, $fileLogs);
    }

    /**
     * Guarda un mensaje de sistema en el log de errores.
     *
     * Reemplaza los marcadores de posición en el mensaje con los argumentos proporcionados.
     *
     * @param string $txt El mensaje de texto que se guardará en el log.
     *                    Puede contener marcadores de posición en el formato %1, %2, etc.
     * @param mixed ...$args Argumentos adicionales que reemplazarán los marcadores de posición en el mensaje.
     *
     * @return void
     */
    public static function saveSystem($txt)
    {
        $argNum  = func_num_args();
        $argList = func_get_args();

        for ($i = 1; $i < $argNum; $i++) {
            $txt = HelperString::replaceFirstOccurrence($txt, "%$i", $argList[$i]);
        }

        error_log(date('[Y-m-d H:i e] ') . $txt . PHP_EOL);
    }

    /**
     * Guarda una excepción en un archivo de log.
     *
     * @param \Exception $ex La excepción que se va a guardar.
     * @param string $txt El mensaje de texto que se va a registrar en el log.
     *
     * @throws \Exception Si no se puede crear el directorio de logs.
     * @throws \Throwable Si ocurre un error al intentar crear el directorio de logs.
     *
     * @return bool false Si ocurre un error al crear el directorio de logs.
     */
    public static function saveExcepcion(\Exception $ex, $txt)
    {
        if (!HelperValidate::isDir(PATH_LOGS)) {
            try {
                if (!mkdir(PATH_LOGS, 0700)) {
                    throw new \Exception('No se pudo crear el directorio de LOGS');
                }
            } catch (\Throwable $th) {
                throw $th;

                return false;
            }
        }

        /* Ruta completa donde ubicar el archivo de logs */
        $fileLogs = PATH_LOGS . 'log-error-' . date('Y-m-d') . '.log';

        $argNum  = func_num_args();
        $argList = func_get_args();

        for ($i = 2; $i < $argNum; $i++) {
            $txt = HelperString::replaceFirstOccurrence($txt, "%$i", $argList[$i]);
        }

        error_log(date('[Y-m-d H:i e]') . ' ERR: ' . $txt . PHP_EOL, 3, $fileLogs);

        if (!empty($ex)) {
            error_log(date('[Y-m-d H:i e]') . ' EXC: ' . $ex->getMessage() . PHP_EOL, 3, $fileLogs);
            error_log(date('[Y-m-d H:i e]') . ' EXC: ' . $ex->getTraceAsString() . PHP_EOL, 3, $fileLogs);
        }
    }

    /**
     * Guarda un mensaje de error en un archivo de log.
     *
     * @param string $txt El mensaje de error a guardar. Puede contener placeholders (%1, %2, etc.) que serán reemplazados por argumentos adicionales.
     *
     * @throws \Exception Si no se puede crear el directorio de logs.
     *
     * @return void
     */
    public static function saveError($txt)
    {
        if (!HelperValidate::isDir(PATH_LOGS)) {
            try {
                if (!mkdir(PATH_LOGS, 0700)) {
                    throw new \Exception('No se pudo crear el directorio de LOGS');
                }
            } catch (\Throwable $th) {
                throw $th;

                return false;
            }
        }

        /* Ruta completa donde ubicar el archivo de logs */
        $fileLogs = PATH_LOGS . 'log-error-' . date('Y-m-d') . '.log';

        $argNum  = func_num_args();
        $argList = func_get_args();

        for ($i = 1; $i < $argNum; $i++) {
            $txt = HelperString::replaceFirstOccurrence($txt, "%$i", $argList[$i]);
        }

        error_log(date('[Y-m-d H:i e]') . ' ERR: ' . $txt . PHP_EOL, 3, $fileLogs);
    }
}
