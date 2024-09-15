<?php

namespace Libraries\BuriPHP\Helpers;

/**
 * Clase abstracta HelperFile
 * 
 * Esta clase proporciona métodos auxiliares relacionados con la manipulación de archivos.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.3
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperFile
{
    /**
     * Escribe texto en un archivo.
     *
     * @param resource $handleFile El manejador del archivo donde se escribirá el texto.
     * @param string $txt El texto que se escribirá en el archivo.
     * @param int|null $maxBytes (Opcional) El número máximo de bytes a escribir. Si es null, se escribe todo el texto.
     * @return int|false El número de bytes escritos, o false en caso de error.
     * @throws \Exception Si no se puede escribir en el archivo.
     */
    public static function write($handleFile, $txt, $maxBytes = null)
    {
        if (is_null($maxBytes)) {
            $ret = fwrite($handleFile, $txt);
        } else {
            $ret = fwrite($handleFile, $txt, $maxBytes);
        }
        if ($ret === false) {
            throw new \Exception('No se puede escribir en el archivo');
        }
        self::flush($handleFile);
        return $ret;
    }

    /**
     * Desbloquea un archivo previamente bloqueado.
     *
     * @param resource $handleFile El manejador del archivo que se desea desbloquear.
     * 
     * @throws \Exception Si no es posible desbloquear el archivo.
     */
    public static function unLock($handleFile)
    {
        if (flock($handleFile, LOCK_UN) === false) {
            throw new \Exception('Imposible desbloquear el archivo');
        }
    }

    /**
     * Trunca el contenido de un archivo al tamaño especificado.
     *
     * @param resource $handleFile El manejador del archivo que se va a truncar.
     * @param int $tamanio El tamaño al que se desea truncar el archivo.
     * @throws \Exception Si no se puede truncar el archivo.
     */
    public static function truncateContent($handleFile, $tamanio)
    {
        if (ftruncate($handleFile, $tamanio) === false) {
            throw new \Exception('No se puede truncar el archivo');
        }
    }

    /**
     * Intenta actualizar la fecha de acceso y modificación del archivo especificado.
     *
     * @param string $filename La ruta del archivo que se desea tocar.
     * @throws \Exception Si no se puede realizar la operación touch en el archivo.
     */
    public static function touch($filename)
    {
        if (touch($filename) === false) {
            throw new \Exception("No se puede realizar un touch al archivo {$filename}");
        }
    }

    /**
     * Desplaza el cursor del archivo al inicio.
     *
     * @param resource $handleFile El manejador del archivo.
     * @throws \Exception Si no se puede desplazar el cursor al inicio del archivo.
     */
    public static function seekToStart($handleFile)
    {
        if (rewind($handleFile) === false) {
            throw new \Exception('No se puede desplazar el cursor al inicio del archivo');
        }
    }

    /**
     * Desplaza el cursor al final del archivo.
     *
     * @param resource $handleFile El manejador del archivo.
     * 
     * @throws \Exception Si no se puede desplazar el cursor al final del archivo.
     * 
     * @return void
     */
    public static function seekToEnd($handleFile)
    {
        $ret = fseek($handleFile, 0, SEEK_END);
        if ($ret === false || $ret == -1) {
            throw new \Exception('No se puede desplazar el cursor al final del archivo');
        }
    }

    /**
     * Desplaza el cursor del archivo en la cantidad especificada por el offset desde la posición actual.
     *
     * @param resource $handleFile El manejador del archivo.
     * @param int $offset La cantidad de bytes a desplazar el cursor.
     * @throws \Exception Si no se puede desplazar el cursor en el archivo.
     * @return void
     */
    public static function seekIncrement($handleFile, $offset)
    {
        $ret = fseek($handleFile, $offset, SEEK_CUR);
        if ($ret === false || $ret == -1) {
            throw new \Exception('No se puede desplazar el cursor en el archivo');
        }
    }

    /**
     * Desplaza el cursor del archivo desde el inicio del archivo.
     *
     * @param resource $handleFile El manejador del archivo.
     * @param int $offset La cantidad de bytes a desplazar desde el inicio del archivo.
     * @throws \Exception Si no se puede desplazar el cursor desde el inicio del archivo.
     * @return void
     */
    public static function seekFromStart($handleFile, $offset)
    {
        $ret = fseek($handleFile, $offset, SEEK_SET);

        if ($ret === false || $ret == -1) {
            throw new \Exception('No se puede desplazar el cursor desde dede el inicio del archivo');
        }
    }

    /**
     * Desplaza el cursor del archivo desde el final del archivo por un número específico de bytes.
     *
     * @param resource $handleFile El manejador del archivo.
     * @param int $offset El número de bytes para desplazar desde el final del archivo.
     * 
     * @throws \Exception Si no se puede desplazar el cursor desde el final del archivo.
     */
    public static function seekFromEnd($handleFile, $offset)
    {
        $ret = fseek($handleFile, (-1 * $offset), SEEK_END);

        if ($ret === false || $ret == -1) {
            throw new \Exception('No se puede desplazar el cursor desde el final del archivo');
        }
    }

    /**
     * Renombra un archivo de origen a un archivo de destino.
     *
     * @param string $sourceFile Ruta del archivo de origen.
     * @param string $targetFile Ruta del archivo de destino.
     * @param bool $overwrite Indica si se debe sobrescribir el archivo de destino si ya existe. Por defecto es false.
     * 
     * @throws \Exception Si el archivo de destino ya existe y no se debe sobrescribir.
     * @throws \Exception Si no se puede renombrar el archivo de origen al archivo de destino.
     */
    public static function rename($sourceFile, $targetFile, $overwrite = false)
    {
        if (!$overwrite && file_exists($targetFile)) {
            throw new \Exception("El archivo destino {$targetFile} ya existe y no se ha de sobreescribir");
        }
        if (rename($sourceFile, $targetFile) === false) {
            throw new \Exception("No se ha podido sobreescribir el archivo origen {$sourceFile} con el archivo destino {$targetFile}");
        }
    }

    /**
     * Elimina archivos en un directorio que coincidan con un patrón específico.
     *
     * @param string $pattern El patrón de búsqueda de los archivos a eliminar.
     * @param string $path La ruta del directorio donde se buscarán los archivos.
     *
     * @return void
     */
    public static function removeByPattern($pattern, $path)
    {
        array_map('unlink', glob($path . $pattern));
    }

    /**
     * Elimina un fichero en la ruta especificada.
     *
     * @param string $filepath La ruta del fichero a eliminar.
     * @throws \Exception Si el fichero no existe o no se puede eliminar.
     */
    public static function deleteFile($filepath)
    {
        if (!file_exists($filepath)) {
            return;
        }
        if (!unlink($filepath)) {
            throw new \Exception("No se puede eliminar el fichero {$filepath}");
        }
    }

    /**
     * Lee y analiza el contenido de un archivo utilizando un formato específico.
     *
     * @param resource $handleFile El manejador del archivo abierto.
     * @param string $format El formato de cadena que se utilizará para analizar el contenido del archivo.
     * @return array|false Devuelve un array con los valores leídos y analizados según el formato especificado, 
     *                     o false en caso de error.
     */
    public static function readScan($handleFile, $format)
    {
        return fscanf($handleFile, $format);
    }

    /**
     * Lee una línea de un archivo.
     *
     * @param resource $handleFile El manejador del archivo abierto.
     * @return string La línea leída del archivo.
     * @throws \Exception Si no se puede leer la línea del archivo.
     */
    public static function readLine($handleFile)
    {
        $ret = fgets($handleFile);
        if ($ret === false) {
            throw new \Exception('No se puede leer la linea del archivo');
        }
        return $ret;
    }

    /**
     * Lee un número específico de bytes de un archivo.
     *
     * @param resource $handleFile El manejador del archivo desde el cual se leerán los bytes.
     * @param int $maxBytes La cantidad máxima de bytes a leer. Por defecto es 1.
     * @return string Los bytes leídos del archivo.
     * @throws \Exception Si no es posible leer los bytes especificados del archivo.
     */
    public static function readChars($handleFile, $maxBytes = 1)
    {
        $ret = fread($handleFile, $maxBytes);
        if ($ret === false) {
            throw new \Exception("Imposible leer {$maxBytes} bytes del archivo");
        }
        return $ret;
    }

    /**
     * Abre un archivo de texto en el modo de acceso especificado.
     *
     * @param string $filepath La ruta del archivo que se desea abrir.
     * @param string $accessMode El modo de acceso en el que se desea abrir el archivo (por ejemplo, 'r' para lectura, 'w' para escritura).
     * @return resource El manejador del archivo abierto.
     * @throws \Exception Si no es posible abrir el archivo en el modo especificado.
     */
    public static function openText($filepath, $accessMode)
    {
        $handle = @fopen($filepath, $accessMode);
        if (!$handle) {
            throw new \Exception("Imposible abrir archivo {$filepath} en modo {$accessMode}");
        }
        return $handle;
    }

    /**
     * Abre un archivo en modo binario.
     *
     * @param string $filepath La ruta del archivo que se desea abrir.
     * @param string $accessMode El modo de acceso en el que se desea abrir el archivo (por ejemplo, 'r' para lectura, 'w' para escritura).
     * @return resource El manejador del archivo abierto.
     * @throws \Exception Si no es posible abrir el archivo en el modo especificado.
     */
    public static function openBinary($filepath, $accessMode)
    {
        $handle = @fopen($filepath, $accessMode . 'b');
        /* b => Abre un binario */
        if (!$handle) {
            throw new \Exception("Imposible abrir archivo {$filepath} en modo {$accessMode}");
        }
        return $handle;
    }

    /**
     * Bloquea un archivo para evitar que otros procesos lo modifiquen simultáneamente.
     *
     * @param resource $handleFile El manejador del archivo que se desea bloquear.
     * 
     * @throws \Exception Si no es posible bloquear el archivo.
     */
    public static function lock($handleFile)
    {
        if (flock($handleFile, LOCK_EX) === false) {
            throw new \Exception('Imposible bloquear el archivo origen');
        }
    }

    /**
     * Verifica si un archivo es escribible.
     *
     * @param string $filename La ruta del archivo a verificar.
     * @return bool Devuelve true si el archivo es escribible, de lo contrario false.
     */
    public static function isWritable($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }
        return is_writable($filename);
    }

    /**
     * Verifica si un archivo es legible.
     *
     * @param string $filename La ruta del archivo a verificar.
     * @return bool Devuelve true si el archivo existe y es legible, de lo contrario devuelve false.
     */
    public static function readable($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }
        return is_readable($filename);
    }

    /**
     * Obtiene el tamaño de un archivo en bytes.
     *
     * @param string $filename La ruta del archivo del cual se desea obtener el tamaño.
     * @return int El tamaño del archivo en bytes, o -1 si el archivo no existe.
     * @throws \Exception Si no se puede obtener el tamaño del archivo.
     */
    public static function getSizeBytes($filename)
    {
        if (!file_exists($filename)) {
            return -1;
        }
        $ret = filesize($filename);
        if ($ret === false) {
            throw new \Exception("No se puede obtener el tamaño del archivo {$filename}");
        }
        return $ret;
    }

    /**
     * Obtiene la posición actual del cursor dentro del archivo.
     *
     * @param resource $handleFile El manejador del archivo.
     * @return int La posición actual del cursor dentro del archivo.
     * @throws \Exception Si no es posible obtener la posición del cursor.
     */
    public static function getSeekPosition($handleFile)
    {
        $ret = ftell($handleFile);
        if ($ret === false) {
            throw new \Exception('Imposible obtener la posición del cursor de dentro del archivo');
        }
        return $ret;
    }

    /**
     * Sanitiza una ruta de archivo reemplazando barras invertidas y redundancias.
     *
     * @param string $filepath La ruta del archivo a sanitizar.
     * @return string La ruta sanitizada.
     */
    public static function getSanitizedPath($filepath)
    {
        $path = HelperString::replaceAll($filepath, '\\', '/');
        $path = HelperString::replaceAll($path, '//', '/');
        $path = HelperString::replaceAll($path, '/./', '/');
        $path = HelperArray::compact(explode('/', $path));

        return "/" . HelperArray::joinValues($path, '/');
    }

    /**
     * Obtiene solo el nombre del archivo de una ruta dada.
     *
     * @param string $filepath La ruta completa del archivo.
     * @return string El nombre del archivo sin la extensión. Si la ruta termina en '/', retorna una cadena vacía.
     */
    public static function getOnlyFileName($filepath)
    {
        return pathinfo($filepath, PATHINFO_FILENAME);
    }

    /**
     * Obtiene solo la extensión de un archivo dado su ruta.
     *
     * @param string $filepath La ruta completa del archivo.
     * @return string La extensión del archivo.
     */
    public static function getOnlyExtension($filepath)
    {
        return pathinfo($filepath, PATHINFO_EXTENSION);
    }

    /**
     * Obtiene solo el nombre del directorio de una ruta de archivo dada.
     *
     * @param string $filepath La ruta del archivo de la cual se desea obtener el nombre del directorio.
     * @return string El nombre del directorio sin el archivo.
     */
    public static function getOnlyDirName($filepath)
    {
        $filepath = self::getSanitizedPath($filepath);
        $lastPos   = strlen($filepath) - 1;
        if ($filepath[$lastPos] == '/') {
            return substr($filepath, 0, $lastPos);
        }
        if ($filepath[0] == '/') {
            $filepath = substr($filepath, 1);
        }

        $dir = pathinfo($filepath, PATHINFO_DIRNAME);

        if ($dir == "\\") {
            $dir = '/';
        } elseif ($dir == '.') {
            $dir = '';
        }
        return $dir;
    }

    /**
     * Obtiene el destino de un enlace simbólico.
     *
     * @param string $nameLink El nombre del enlace simbólico.
     * @return string El destino del enlace simbólico si existe, de lo contrario una cadena vacía.
     */
    public static function getLinkTarget($nameLink)
    {
        if (!is_link($nameLink)) {
            return '';
        }
        return '' . readlink($nameLink);
    }

    /**
     * Obtiene información sobre un archivo especificado.
     *
     * @param string $file La ruta del archivo del cual se desea obtener información.
     * @param array|string $returnedValues Valores que se desean obtener sobre el archivo. 
     *        Puede ser un array o una cadena separada por comas. Los valores posibles son:
     *        - 'name': Nombre del archivo.
     *        - 'server_path': Ruta completa del archivo en el servidor.
     *        - 'size': Tamaño del archivo en bytes.
     *        - 'date': Fecha de creación del archivo.
     *        - 'readable': Indica si el archivo es legible.
     *        - 'writable': Indica si el archivo es escribible.
     *        - 'executable': Indica si el archivo es ejecutable.
     *        - 'fileperms': Permisos del archivo.
     * @return array Información del archivo basada en los valores solicitados.
     */
    public static function getInfo($file, $returnedValues = array('name', 'server_path', 'size', 'date'))
    {
        $fileinfo = [];
        if (!file_exists($file)) {
            return $fileinfo;
        }

        if (is_string($returnedValues)) {
            $returnedValues = explode(',', $returnedValues);
        }

        foreach ($returnedValues as $key) {
            switch ($key) {
                case 'name':
                    $fileinfo['name'] = substr(strrchr($file, DS), 1);
                    break;
                case 'server_path':
                    $fileinfo['server_path'] = $file;
                    break;
                case 'size':
                    $fileinfo['size'] = filesize($file);
                    break;
                case 'date':
                    $fileinfo['date'] = filectime($file);
                    break;
                case 'readable':
                    $fileinfo['readable'] = is_readable($file);
                    break;
                case 'writable':
                    // There are known problems using is_weritable on 
                    // IIS.  It may not be reliable - consider 
                    // fileperms()
                    $fileinfo['writable'] = is_writable($file);
                    break;
                case 'executable':
                    $fileinfo['executable'] = is_executable($file);
                    break;
                case 'fileperms':
                    $fileinfo['fileperms'] = fileperms($file);
                    break;
            }
        }

        return $fileinfo;
    }

    /**
     * Crea un archivo temporal en el directorio temporal del sistema.
     *
     * @param string $prefix Prefijo opcional para el nombre del archivo temporal.
     * @return string Nombre del archivo temporal creado.
     * @throws \Exception Si no es posible crear el archivo temporal.
     */
    public static function getFileTmp($prefix = '')
    {
        $name = tempnam(sys_get_temp_dir(), $prefix);
        if ($name === false) {
            throw new \Exception('Imposible crear archivo temporal');
        }
        return $name;
    }

    /**
     * Obtiene el nombre completo del archivo desde una ruta dada.
     *
     * @param string $filepath La ruta completa del archivo.
     * @return string El nombre completo del archivo. Si la ruta termina en '/', retorna una cadena vacía.
     */
    public static function getFileNameFull($filepath)
    {
        $filepath = self::getSanitizedPath($filepath);
        $lastPos   = strlen($filepath) - 1;
        if ($filepath[$lastPos] == '/') {
            return '';
        }
        return pathinfo($filepath, PATHINFO_BASENAME);
    }

    /**
     * Obtiene todo el contenido de un archivo.
     *
     * @param string $filepath La ruta del archivo del cual se obtendrá el contenido.
     * @param int|null $offset (Opcional) El punto de inicio desde donde se leerá el archivo.
     * @param int|null $maxBytes (Opcional) El número máximo de bytes a leer desde el archivo.
     * 
     * @return string El contenido del archivo.
     * 
     * @throws \Exception Si no es posible leer el contenido del archivo.
     */
    public static function getAllContent($filepath, $offset = null, $maxBytes = null)
    {
        if (!is_null($offset) && !is_null($maxBytes)) {
            $ret = file_get_contents(
                $filepath,
                false,
                null,
                $offset,
                $maxBytes
            );
        } else {
            $ret = file_get_contents($filepath);
        }
        if ($ret === false) {
            throw new \Exception("Imposible leer el contenido del archivo {$filepath}");
        }
        return $ret;
    }

    /**
     * Fuerza a que se escriban todos los datos pendientes en el archivo.
     *
     * @param resource $handleFile El manejador del archivo que se va a vaciar.
     * 
     * @throws \Exception Si no se puede forzar la escritura del archivo.
     */
    public static function flush($handleFile)
    {
        if (fflush($handleFile) === false) {
            throw new \Exception('No sd puede formazar la escriptura del archivo');
        }
    }

    /**
     * Verifica si un archivo existe en la ruta especificada.
     *
     * @param string $filepath La ruta del archivo a verificar.
     * @return bool Devuelve true si el archivo existe, de lo contrario false.
     */
    public static function exists($filepath)
    {
        return file_exists($filepath);
    }

    /**
     * Verifica si se ha alcanzado el final del archivo.
     *
     * @param resource $handleFile El manejador del archivo que se está leyendo.
     * @return bool Devuelve true si se ha alcanzado el final del archivo, de lo contrario false.
     */
    public static function eof($handleFile)
    {
        return feof($handleFile);
    }

    /**
     * Crea un enlace simbólico (symlink) desde un archivo de origen a un destino.
     *
     * @param string $sourceFilename Ruta del archivo de origen.
     * @param string $nameLinkDestination Ruta del enlace simbólico de destino.
     * @throws \Exception Si no es posible crear el enlace simbólico.
     */
    public static function createLink($sourceFilename, $nameLinkDestination)
    {
        $ret = symlink($sourceFilename, $nameLinkDestination);
        if ($ret === false) {
            throw new \Exception("Imposible crear link destino {$nameLinkDestination} del archivo origen {$sourceFilename}");
        }
    }

    /**
     * Copia un archivo de una ubicación a otra.
     *
     * @param string $sourceFile Ruta del archivo origen.
     * @param string $targetFile Ruta del archivo destino.
     * @param bool $overwrite Indica si se debe sobrescribir el archivo destino si ya existe. Por defecto es false.
     * 
     * @throws \Exception Si el archivo destino ya existe y no se debe sobrescribir.
     * @throws \Exception Si no se puede copiar el archivo origen al archivo destino.
     */
    public static function copy($sourceFile, $targetFile, $overwrite = false)
    {
        if (!$overwrite && file_exists($targetFile)) {
            throw new \Exception("El archivo destino {$targetFile} ya existe y no se ha de sobreescribir");
        }
        if (copy($sourceFile, $targetFile) === false) {
            throw new \Exception("No se puede copiar el archivo origen {$sourceFile} en el archivo destino {$targetFile}");
        }
    }

    /**
     * Mueve un archivo desde una ubicación de origen a una ubicación de destino.
     *
     * @param string $sourceFile La ruta del archivo de origen.
     * @param string $targetFile La ruta del archivo de destino.
     * @param bool $overwrite (Opcional) Si se debe sobrescribir el archivo de destino si ya existe. Por defecto es false.
     * 
     * @throws \Exception Si el archivo de destino ya existe y no se debe sobrescribir.
     * @throws \Exception Si no se puede mover el archivo de origen al archivo de destino.
     */
    public static function move($sourceFile, $targetFile, $overwrite = false)
    {
        if (!$overwrite && file_exists($targetFile)) {
            throw new \Exception("El archivo destino {$targetFile} ya existe y no se ha de sobreescribir");
        }
        if (move_uploaded_file($sourceFile, $targetFile) === false) {
            throw new \Exception("No se puede mover el archivo origen {$sourceFile} en el archivo destino {$targetFile}");
        }
    }

    /**
     * Cierra un archivo abierto.
     *
     * @param resource $handleFile El manejador del archivo que se desea cerrar.
     * @return void
     */
    public static function close($handleFile)
    {
        @fclose($handleFile);
    }

    /**
     * Cambia la extensión de un archivo dado.
     *
     * @param string $filename El nombre del archivo al que se le cambiará la extensión.
     * @param string $newExtension La nueva extensión que se aplicará al archivo.
     * @return string El nombre del archivo con la nueva extensión.
     *
     * Si la nueva extensión está vacía, se devuelve el nombre del archivo original.
     * Si la nueva extensión no comienza con un punto, se le agrega uno automáticamente.
     * Si el nombre del archivo no contiene un nombre de archivo, se devuelve el nombre del archivo original.
     * Si el directorio está vacío y el nombre del archivo no comienza con una barra, se devuelve el nombre del archivo con la nueva extensión.
     */
    public static function changeExtension($filename, $newExtension)
    {
        if (HelperValidate::isEmpty($newExtension)) {
            return $filename;
        }

        if ($newExtension[0] != '.') {
            $newExtension = '.' . $newExtension;
        }

        $dir = self::getOnlyDirName($filename);

        $name = self::getOnlyFileName($filename);

        if ($name == '') {
            return $filename;
        }

        if ($dir == '' && $filename[0] != '/') {
            return $dir . $name . $newExtension;
        }

        return $dir . '/' . $name . $newExtension;
    }

    /**
     * Obtiene los tipos MIME soportados.
     *
     * @param string|bool $mime El tipo MIME a buscar. Si es false, se devuelve la lista completa de tipos MIME soportados.
     * 
     * @return array|string Devuelve un array con los tipos MIME soportados si $mime es false. Si $mime es un string, devuelve el tipo MIME correspondiente o un array con un mensaje de error si no se encuentra.
     * 
     * @throws \Exception Si el tipo MIME no es soportado.
     */
    public static function getSupportedMime($mime = false)
    {
        $supported = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
            'flif' => 'image/flif',
            'flv' => 'video/x-flv',
            'js' => 'application/x-javascript',
            'json' => 'application/json',
            'tiff' => 'image/tiff',
            'css' => 'text/css',
            'xml' => 'application/xml',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'xlt' => 'application/vnd.ms-excel',
            'xlm' => 'application/vnd.ms-excel',
            'xld' => 'application/vnd.ms-excel',
            'xla' => 'application/vnd.ms-excel',
            'xlc' => 'application/vnd.ms-excel',
            'xlw' => 'application/vnd.ms-excel',
            'xll' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pps' => 'application/vnd.ms-powerpoint',
            'rtf' => 'application/rtf',
            'pdf' => 'application/pdf',
            'html' => 'text/html',
            'htm' => 'text/html',
            'php' => 'text/html',
            'txt' => 'text/plain',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'mp3' => 'audio/mpeg3',
            'wav' => 'audio/wav',
            'aiff' => 'audio/aiff',
            'aif' => 'audio/aiff',
            'avi' => 'video/msvideo',
            'wmv' => 'video/x-ms-wmv',
            'mov' => 'video/quicktime',
            'zip' => 'application/zip',
            'tar' => 'application/x-tar',
            'swf' => 'application/x-shockwave-flash',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ott' => 'application/vnd.oasis.opendocument.text-template',
            'oth' => 'application/vnd.oasis.opendocument.text-web',
            'odm' => 'application/vnd.oasis.opendocument.text-master',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'otg' => 'application/vnd.oasis.opendocument.graphics-template',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'otp' => 'application/vnd.oasis.opendocument.presentation-template',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'odc' => 'application/vnd.oasis.opendocument.chart',
            'odf' => 'application/vnd.oasis.opendocument.formula',
            'odb' => 'application/vnd.oasis.opendocument.database',
            'odi' => 'application/vnd.oasis.opendocument.image',
            'oxt' => 'application/vnd.openofficeorg.extension',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
            'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'thmx' => 'application/vnd.ms-officetheme',
            'onetoc' => 'application/onenote',
            'onetoc2' => 'application/onenote',
            'onetmp' => 'application/onenote',
            'onepkg' => 'application/onenote',
            'csv' => 'text/csv',
        ];

        if ($mime !== false) {
            try {
                $foo = HelperArray::indexOfKey($supported, $mime);

                if ($foo >= 0) {
                    return $supported[$mime];
                } else {
                    throw new \Exception("file_not_supported");
                }
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'code' => $e->getMessage(),
                ];
            }
        } else {
            return $supported;
        }
    }

    /**
     * Sube un archivo al servidor.
     *
     * @param array $args Argumentos para la función.
     *      - 'file' (array): Archivo a subir. Debe contener las claves 'name' y 'tmp_name'.
     *      - 'path' (string): Ruta donde se copiará el archivo.
     *      - 'supportedExt' (array, opcional): Extensiones de archivo soportadas.
     *
     * @return string|array Ruta del archivo subido o un array con 'status' y 'message' en caso de error.
     *
     * @throws \Exception Si no se envía ningún archivo.
     * @throws \Exception Si no se establece la ruta para copiar el archivo.
     * @throws \Exception Si el archivo no es soportado.
     */
    public static function uploadFile(...$args)
    {
        try {
            if (!isset($args['file']) || empty($args['file'])) {
                throw new \Exception("No se envío ningún archivo.");
            } else {
                $file = $args['file'];
            }

            if (!isset($args['path']) || empty($args['path'])) {
                throw new \Exception("No se estableció la ruta para copiar el archivo.");
            } else {
                $path = self::getSanitizedPath($args['path']);
            }

            $mime = self::getSupportedMime(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (is_array($mime) && $mime['status'] === 'error') {
                throw new \Exception("El archivo no es soportado.");
            }

            if (
                isset($args['supportedExt'])
                && is_array($args['supportedExt'])
                && HelperArray::indexOfValue($args['supportedExt'], pathinfo($file['name'], PATHINFO_EXTENSION)) < 0
            ) {
                throw new \Exception("El archivo no es soportado.");
            }

            $path = self::getSanitizedPath($path . '/' . pathinfo($file['name'], PATHINFO_FILENAME) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION));

            self::copy($file['tmp_name'], $path);

            return $path;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
