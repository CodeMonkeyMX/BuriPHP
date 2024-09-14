<?php

/**
 * @package BuriPHP.Libraries.Helpers
 * 
 * @abstract
 *
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

namespace Libraries\BuriPHP\Helpers;

abstract class HelperLog
{
    /**
     * Guarda un rastro (trace) en un archivo de log.
     *
     * @param string $txt El texto a guardar en el log. Puede contener placeholders (%1, %2, etc.) que serán reemplazados por los argumentos adicionales.
     *
     * @throws \Exception Si no se puede crear el directorio de logs.
     *
     * @return void
     */
    public static function saveTrace($txt)
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
        $fileLogs = PATH_LOGS . 'log-' . date('Y-m-d') . '.log';

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
