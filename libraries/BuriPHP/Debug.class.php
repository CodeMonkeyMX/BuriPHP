<?php

namespace Libraries\BuriPHP;

use Libraries\BuriPHP\Helpers\HelperConvert;

/**
 * Clase abstracta Debug
 * 
 * Esta clase proporciona funcionalidades de depuración para la aplicación.
 * 
 * @package BuriPHP
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class Debug
{
    /**
     * Muestra una alerta en el navegador con el texto proporcionado.
     *
     * @param string $txt El texto que se mostrará en la alerta.
     */
    public static function alert($txt)
    {
        echo "<script>alert( '" . addslashes($txt) . "' );</script>";
    }

    /**
     * Imprime el texto proporcionado seguido de un salto de línea.
     *
     * @param string $txt El texto que se va a imprimir.
     */
    public static function echo($txt)
    {
        echo $txt . "\n";
    }

    /**
     * Imprime el texto proporcionado, ya sea como un array o en formato JSON.
     *
     * @param mixed $txt El texto que se va a convertir e imprimir.
     * @param bool $json (Opcional) Si es true, el texto se imprimirá en formato JSON. Por defecto es false.
     *
     * @return void
     */
    public static function print($txt, $json = false)
    {
        $r = (!$json) ? HelperConvert::toArray($txt) : json_encode(HelperConvert::toArray($txt));

        print_R($r);
    }

    /**
     * Muestra el contenido formateado dentro de etiquetas <pre>.
     *
     * @param mixed $txt El contenido a mostrar.
     */
    public static function pre($txt)
    {
        echo "<pre>";
        self::print($txt);
        echo "</pre>\n";
    }

    /**
     * Abre una nueva ventana del navegador y muestra el contenido formateado de la variable proporcionada.
     *
     * @param mixed $txt La variable que se desea imprimir y visualizar en una nueva ventana del navegador.
     *
     * El contenido de la variable se convierte en una cadena con formato HTML, se escapan los caracteres especiales
     * y se reemplazan los saltos de línea por etiquetas <br>. Luego, se abre una nueva ventana del navegador y se 
     * muestra el contenido formateado dentro de un elemento <pre>.
     */
    public static function pr($txt)
    {
        $txt = '<pre>' . addslashes(nl2br(print_r($txt, true))) . '</pre>';
        $txt = str_replace(array("\r", "\n", "\r\n"), array(''), $txt);

        echo '<script>';
        echo 'w=window.open( "","_blank","toolbar=yes, location=yes, directories=no, status=yes, menubar=yes, scrollbars=yes, resizable=yes, copyhistory=yes" );';
        echo 'w.document.write( "<html lang=\"es\"> <head> <title>debug</title> </head> <body>' . $txt . '</body>" );';
        echo 'w.document.close( );';
        echo '</script>';
    }
}
