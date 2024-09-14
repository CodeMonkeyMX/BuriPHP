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

abstract class HelperImage
{
    /**
     * Genera una miniatura de una imagen dada.
     *
     * @param string $filename Ruta del archivo de la imagen original.
     * @param string $nameThumbnail Nombre del archivo de la miniatura generada.
     * @param float $proportion Proporción de reducción de la imagen original.
     * @param int $quality Calidad de la miniatura generada (por defecto 75).
     * @return bool Devuelve true si la miniatura se genera correctamente, false en caso contrario.
     */
    public static function generateThumbnail($filename, $nameThumbnail, $proportion, $quality = 75)
    {
        if (!file_exists($filename)) {
            return false;
        }

        // Miramos que extensión tiene
        $extension = HelperString::toLower(
            HelperFile::getOnlyExtension($filename)
        );
        if (HelperValidate::isEmpty($extension)) {
            return false;
        }

        if ($extension == 'png') {
            $img = imagecreatefrompng($filename);
        } elseif ($extension = 'jpg') {
            $img = imagecreatefromjpeg($filename);
        } else {
            // Otra extensión
            return false;
        }

        // Dimensiones actuales de la imagen
        $currentWidth = imagesx($img);
        $currentHeight  = imagesy($img);

        // Calculamos las dimenstions de la miniatura
        $miniWidth = $currentWidth * $proportion;
        $miniHeight  = $currentHeight * $proportion;

        // Creamos imagen contenedor con la misma proporción
        $imgMini = imagecreatetruecolor($miniWidth, $miniHeight);

        // Copiamos la imagen original a la reducida
        imagecopyresized($imgMini, $img, 0, 0, 0, 0, $miniWidth, $miniHeight, $currentWidth, $currentHeight);

        // Generamos la miniatura
        imagejpeg($imgMini, $nameThumbnail, $quality);
        return true;
    }
}
