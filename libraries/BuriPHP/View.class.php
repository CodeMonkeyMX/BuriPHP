<?php

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperFile;
use Libraries\BuriPHP\Helpers\HelperServer;
use Libraries\BuriPHP\Helpers\HelperString;

/**
 * Clase View
 * 
 * Esta clase es responsable de manejar la lógica de la vista en la aplicación.
 * Se encarga de renderizar las plantillas y pasar los datos necesarios a las vistas.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 1.0
 * @version 2.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class View
{
    /**
     * Renderiza una vista y la devuelve como una cadena.
     *
     * @param string $file Ruta del archivo de vista a renderizar.
     * @param string|null|false $base Ruta del archivo base para la vista. 
     *                                Si es false, solo se muestra el archivo de vista.
     *                                Si es null, se usa el archivo base por defecto.
     *                                Si no es null, se usa el archivo base especificado.
     * @return string La vista renderizada como una cadena.
     */
    final public function render($file, $base = null)
    {
        foreach ($GLOBALS as $key => $value) {
            if (
                $key != 'GLOBALS' ||
                $key != '_SERVER' ||
                $key != '_GET' ||
                $key != '_POST' ||
                $key != '_FILES' ||
                $key != '_COOKIE' ||
                $key != '_SESSION' ||
                $key != '_REQUEST' ||
                $key != '_ENV' ||
                $key != '_APP'
            ) {
                global ${$key};
            }
        }

        /**
         * Obtiene la vista.
         */
        ob_start();
        require HelperFile::getSanitizedPath($file);
        $renderBody = ob_get_contents();
        ob_end_clean();

        /**
         * Obtiene la base principal de las vistas.
         * 
         * Si $base es false, solo debe mostrar $file
         * Si $base es null, debe agregar el default
         * Si $base es no empty, debe usar el que se esta solicitando
         */
        if ($base !== false) {
            ob_start();

            if (is_null($base)) {
                require HelperFile::getSanitizedPath(PATH_SHARED . 'base.php');
            }

            if (!is_null($base)) {
                require HelperFile::getSanitizedPath($base);
            }

            $renderBase = ob_get_contents();

            ob_end_clean();

            $renderBody = str_replace('{{renderView}}', $renderBody, $renderBase);
        }

        $renderBody = $this->includesModals($renderBody);
        $renderBody = $this->importFiles($renderBody);
        $renderBody = $this->replaceVars($renderBody);
        $renderBody = $this->replacePaths($renderBody);
        $renderBody = $this->includesDependencies($renderBody);

        // Eliminamos saltos de linea en blanco.
        $renderBody = preg_replace("/[\r\n]+/", PHP_EOL, $renderBody);

        return $renderBody;
    }

    /**
     * Establece el título de la página.
     *
     * Esta función final pública establece el título de la página en la variable global
     * '_APP' utilizando la serialización del string proporcionado.
     *
     * @param string $str El título de la página a establecer.
     * @return void
     */
    final public function setPageTitle($str)
    {
        $GLOBALS['_APP']['pageTitle'] = serialize($str);
    }

    /**
     * Obtiene el título de la página desde la variable global '_APP'.
     *
     * @return string El título de la página si está definido, de lo contrario una cadena vacía.
     */
    private function getPageTitle()
    {
        if (!isset($GLOBALS['_APP']['pageTitle'])) {
            return '';
        }

        return unserialize($GLOBALS['_APP']['pageTitle']);
    }

    /**
     * Importa archivos en la vista reemplazando los marcadores de posición.
     *
     * Este método busca todos los marcadores de posición en la vista que coincidan con el patrón `{{import|ruta/al/archivo}}`.
     * Luego, intenta importar el contenido de los archivos especificados y reemplaza los marcadores de posición con el contenido del archivo.
     * Si el archivo no existe, el marcador de posición se reemplaza con una cadena vacía.
     *
     * @param string $view La vista que contiene los marcadores de posición para importar archivos.
     * @return string La vista con los archivos importados y los marcadores de posición reemplazados.
     */
    private function importFiles($view)
    {
        preg_match_all("/\{{2}import\|[a-zA-Z_\/\.]+\}{2}/", $view, $placeholders, PREG_SET_ORDER);

        foreach ($placeholders as $value) {
            $nameFile = HelperString::getBetween($value[0], '{{import|', '}}');
            $file = explode('/', $nameFile);
            $file = HelperArray::compact($file);

            $file = (count($file) > 1) ?
                HelperFile::getSanitizedPath(PATH_ROOT . DS . HelperArray::joinValues($file, '/')) :
                HelperFile::getSanitizedPath(PATH_SHARED . HelperArray::joinValues($file, '/'));

            if (HelperFile::exists($file)) {
                foreach ($GLOBALS as $key => $value) {
                    if (
                        $key != 'GLOBALS' ||
                        $key != '_SERVER' ||
                        $key != '_GET' ||
                        $key != '_POST' ||
                        $key != '_FILES' ||
                        $key != '_COOKIE' ||
                        $key != '_SESSION' ||
                        $key != '_REQUEST' ||
                        $key != '_ENV' ||
                        $key != '_APP'
                    ) {
                        global ${$key};
                    }
                }

                ob_start();
                require $file;
                $import = ob_get_contents();
                ob_end_clean();
                $view = str_replace('{{import|' . $nameFile . '}}', $import, $view);
                $view = self::importFiles($view);
                unset($import);
            } else {
                $view = str_replace('{{import|' . $nameFile . '}}', '', $view);
            }
        }

        return $view;
    }

    /**
     * Reemplaza las variables en la vista con sus valores correspondientes.
     *
     * @param string $view El contenido de la vista en el que se reemplazarán las variables.
     * @return string El contenido de la vista con las variables reemplazadas por sus valores.
     */
    private function replaceVars($view)
    {
        $varArr = [
            '{$pageTitle}' => $this->getPageTitle(),
            '{$pageBase}' => HelperServer::getDomainHttp()
        ];

        $view = str_replace(array_keys($varArr), array_values($varArr), $view);

        return $view;
    }

    /**
     * Reemplaza las rutas de los recursos en la vista proporcionada.
     *
     * Este método busca patrones específicos en la vista que coincidan con 
     * `{{path|tipo}}` y los reemplaza con las rutas correspondientes a los 
     * recursos (CSS, JS, imágenes, subidas y plugins).
     *
     * @param string $view La vista en la que se reemplazarán las rutas.
     * @return string La vista con las rutas reemplazadas.
     */
    private function replacePaths($view)
    {
        preg_match_all("/\{{2}path\|[a-zA-Z]+\}{2}/", $view, $path, PREG_SET_ORDER);

        foreach ($path as $value) {
            $asset = HelperString::getBetween($value[0], '{{path|', '}}');

            switch ($asset) {
                case 'css':
                    $view = str_replace('{{path|css}}', '/assets/css/', $view);
                    break;

                case 'js':
                    $view = str_replace('{{path|js}}', '/assets/js/', $view);
                    break;

                case 'image':
                    $view = str_replace('{{path|image}}', '/assets/images/', $view);
                    break;

                case 'upload':
                    $view = str_replace('{{path|upload}}', '/assets/uploads/', $view);
                    break;

                case 'plugin':
                    $view = str_replace('{{path|plugin}}', '/assets/plugins/', $view);
                    break;
            }
        }

        return $view;
    }

    /**
     * Incluye las dependencias de CSS y JS en la vista proporcionada.
     *
     * Este método busca patrones específicos en la vista para identificar
     * dependencias de archivos CSS y JS, y luego las incluye en la vista.
     *
     * @param string $view La vista en la que se incluirán las dependencias.
     * @return string La vista con las dependencias de CSS y JS incluidas.
     */
    private function includesDependencies($view)
    {
        preg_match_all("/\{{2}asset\|[a-zA-Z\|\{\$-_=?\.\}\:\/]+}{2}/", $view, $includes, PREG_SET_ORDER);

        $dependencies = [
            'css' => [],
            'js' => []
        ];

        foreach ($includes as $value) {
            $file = HelperString::getBetween($value[0], '{{asset|', '}}');
            $file = explode('|', $file);
            $file = HelperArray::compact($file);

            foreach ($file as $_key => $_value) {
                if ($_key !== 1) {
                    $x = explode(':', $_value);

                    if (count($x) == 2) {
                        $file[$_key] = $x[0] . '="' . $x[1] . '"';
                    }
                }
            }

            switch ($file[0]) {
                case 'css':
                    $attributes = "";

                    for ($i = 2; $i < count($file); $i++) {
                        $attributes .= " " . $file[$i];
                    }

                    $dependencies['css'][] = '<link href="' . $file[1] . '"' . $attributes . '/>';
                    break;
                case 'js':
                    $attributes = "";

                    for ($i = 2; $i < count($file); $i++) {
                        $attributes .= " " . $file[$i];
                    }

                    $dependencies['js'][] = '<script src="' . $file[1] . '"' . $attributes . '></script>';
                    break;
            }

            $view = str_replace($value[0], '', $view);
        }

        foreach ($dependencies['css'] as $value) {
            $view = str_replace('{$cssDependencies}', $value . '{$cssDependencies}', $view);
        }

        $view = str_replace('{$cssDependencies}', '', $view);

        foreach ($dependencies['js'] as $value) {
            $view = str_replace('{$jsDependencies}', $value . '{$jsDependencies}', $view);
        }

        $view = str_replace('{$jsDependencies}', '', $view);

        return $view;
    }

    /**
     * Incluye modales en la vista proporcionada.
     *
     * Este método busca y procesa todos los placeholders de modales en la vista,
     * carga el contenido de los archivos de modales correspondientes y los inserta
     * en la vista. Los placeholders de modales tienen el formato `{{modal|ruta/al/archivo}}`.
     *
     * @param string $view La vista en la que se incluirán los modales.
     * @return string La vista con los modales incluidos.
     */
    private function includesModals($view)
    {
        preg_match_all("/\{{2}modal\|[a-zA-Z_\/\.]+\}{2}/", $view, $placeholders, PREG_SET_ORDER);
        $modals = "";

        foreach ($placeholders as $value) {
            $nameFile = HelperString::getBetween($value[0], '{{modal|', '}}');
            $file = explode('/', $nameFile);
            $file = HelperArray::compact($file);

            $file = (count($file) > 1) ?
                HelperFile::getSanitizedPath(PATH_ROOT . DS . HelperArray::joinValues($file, '/')) :
                HelperFile::getSanitizedPath(PATH_SHARED . HelperArray::joinValues($file, '/'));

            if (HelperFile::exists($file)) {
                ob_start();
                require $file;
                $modal = ob_get_contents();
                ob_end_clean();
                $modals .= $modal;
                unset($modal);
            }

            $view = str_replace('{{modal|' . $nameFile . '}}', '', $view);
        }

        $view = str_replace('{{renderModals}}', $modals, $view);

        return $view;
    }
}
