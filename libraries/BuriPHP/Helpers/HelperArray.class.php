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

abstract class HelperArray
{
    /**
     * Ordena un array multidimensional basado en múltiples campos.
     *
     * Esta función toma un número variable de argumentos. El primer argumento es el array
     * multidimensional que se desea ordenar. Los argumentos subsecuentes son los nombres
     * de los campos por los cuales se desea ordenar el array.
     *
     * @return array El array ordenado.
     */
    function sortMultiValue()
    {
        $args = func_get_args();
        $data = array_shift($args);

        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }

        $args[] = &$data;
        call_user_func_array('sortMultiValue', $args);

        return array_pop($args);
    }

    /**
     * Ordena un array en orden ascendente o descendente.
     *
     * @param array $arr El array a ordenar.
     * @param string $order El orden de la ordenación, puede ser 'ASC' para ascendente o 'DESC' para descendente. 
     *                      El valor predeterminado es 'ASC'.
     * @return array El array ordenado.
     */
    public static function sort($arr, $order = 'ASC')
    {
        $arrayTmp = HelperConvert::toArray($arr);

        switch (strtoupper($order)) {
            default:
            case 'ASC':
                asort($arrayTmp);
                break;

            case 'DESC':
                arsort($arrayTmp);
                break;
        }

        return $arrayTmp;
    }

    /**
     * Elimina la primera ocurrencia de un valor específico de un array.
     *
     * @param array $arr El array del cual se eliminará el valor.
     * @param mixed $val El valor que se eliminará del array.
     * @return array El array resultante después de eliminar la primera ocurrencia del valor especificado.
     */
    public static function removeValueFirst($arr, $val)
    {
        $arrayTmp = [];
        $found = false;

        foreach (HelperConvert::toArray($arr) as $key => $value) {
            if (!$found && $value == $val) {
                $found = true;
                continue;
            }
            $arrayTmp[$key] = $value;
        }

        return $arrayTmp;
    }

    /**
     * Elimina todas las ocurrencias de un valor específico de un array.
     *
     * @param array $arr El array del cual se eliminarán los valores.
     * @param mixed $sStr El valor que se eliminará del array.
     * @return array El array resultante después de eliminar las ocurrencias del valor especificado.
     */
    public static function removeValueAll($arr, $sStr)
    {
        return array_filter(HelperConvert::toArray($arr), function ($value) use ($sStr) {
            return $value !== $sStr;
        });
    }

    /**
     * Elimina un elemento de un array en una posición específica.
     *
     * @param array $arr El array del cual se eliminará el elemento.
     * @param int $pos La posición del elemento que se desea eliminar.
     * @return array El array resultante después de eliminar el elemento en la posición especificada.
     */
    public static function removeByPosition($arr, $pos)
    {
        if (!is_array($arr) || $pos < 0 || $pos >= count($arr)) {
            return $arr;
        }

        array_splice($arr, $pos, 1);
        return $arr;
    }

    /**
     * Elimina una clave específica de un array.
     *
     * @param array $arr El array del cual se eliminará la clave.
     * @param mixed $deleteKey La clave que se desea eliminar del array.
     * @return array El array resultante después de eliminar la clave especificada.
     */
    public static function removeKey($arr, $deleteKey)
    {
        if (array_key_exists($deleteKey, HelperConvert::toArray($arr))) {
            unset($arr[$deleteKey]);
        }

        return $arr;
    }

    /**
     * Elimina los elementos duplicados de un array.
     *
     * @param array $arr El array del cual se eliminarán los duplicados.
     * @return array Un array sin elementos duplicados.
     */
    public static function removeDuplicates($arr)
    {
        return array_unique(HelperConvert::toArray($arr));
    }

    /**
     * Une los valores de un array en una cadena, separados por un delimitador especificado.
     *
     * @param array|string $arr El array o cadena a unir.
     * @param string $separador El delimitador que se utilizará para separar los valores. Por defecto es una coma (,).
     * @return string La cadena resultante con los valores unidos por el delimitador.
     */
    public static function joinValues($arr, $separador = ',')
    {
        return join($separador, HelperConvert::toArray($arr));
    }

    /**
     * Encuentra el índice de un valor en un array.
     *
     * Esta función busca el índice de un valor específico en un array. 
     * La búsqueda es sensible a mayúsculas y minúsculas, por lo que 
     * convierte todos los valores del array a minúsculas antes de 
     * realizar la comparación.
     *
     * @param array|string $arr El array o cadena que se convertirá a array.
     * @param string $value El valor que se busca en el array.
     * @return int El índice del valor en el array, o -1 si no se encuentra.
     */
    public static function indexOfValue($arr, $value)
    {
        $arr = HelperConvert::toArray($arr);
        if (count($arr) == 0) {
            return -1;
        }

        // Como la función es sensitiva a mayúsculas i minúsculas
        // hacemos una copia de array poniendo 
        // las claves en minúsculas
        // la comparación se hace en minúsculas.
        $arrayCopy = array_map(function ($val) {
            return HelperString::toLower($val);
        }, array_values($arr));

        $pos = array_search($value, $arrayCopy);
        if (false === $pos) {
            return -1;
        }

        return $pos;
    }

    /**
     * Encuentra el índice de una clave específica en un arreglo.
     *
     * @param array $arr El arreglo en el cual buscar la clave.
     * @param mixed $key La clave que se desea encontrar.
     * @return int El índice de la clave en el arreglo, o -1 si la clave no se encuentra.
     */
    public static function indexOfKey($arr, $key)
    {
        $arr = HelperConvert::toArray($arr);
        if (count($arr) == 0) {
            return -1;
        }

        $key = HelperString::toLower(HelperString::removeAccents($key));

        $arrayCopy = array_map(function ($val) {
            return HelperString::toLower(HelperString::removeAccents($val));
        }, array_keys($arr));

        $pos = array_search($key, $arrayCopy);
        if ($pos === false) {
            return -1;
        }

        return $pos;
    }

    /**
     * Obtiene el valor de un arreglo en una posición específica.
     *
     * @param array $arr El arreglo del cual se obtendrá el valor.
     * @param int $pos La posición del valor que se desea obtener.
     * @param mixed $default Valor por defecto a retornar si la posición no existe en el arreglo.
     * @return mixed El valor en la posición especificada del arreglo, o el valor por defecto si la posición no existe.
     */
    public static function getValueByPosition($arr, $pos, $default = null)
    {
        $arr = HelperConvert::toArray($arr);
        return isset($arr[$pos]) ? $arr[$pos] : $default;
    }

    /**
     * Obtiene el valor de un arreglo asociativo dado una clave específica.
     *
     * Convierte el arreglo a un formato adecuado y normaliza la clave
     * eliminando acentos y convirtiéndola a minúsculas para realizar una
     * comparación insensible a mayúsculas y minúsculas.
     *
     * @param array|string $arr El arreglo asociativo o una representación
     *                          que puede ser convertida a un arreglo.
     * @param string $key La clave cuyo valor se desea obtener.
     * @return mixed El valor asociado a la clave especificada, o null si
     *               la clave no se encuentra en el arreglo.
     */
    public static function getValueByKey($arr, $key)
    {
        $arr = HelperConvert::toArray($arr);
        $key = HelperString::toLower(HelperString::removeAccents($key));

        foreach ($arr as $k => $v) {
            if (HelperString::toLower(HelperString::removeAccents($k)) == $key) {
                return $v;
            }
        }

        return null;
    }

    /**
     * Obtiene una submatriz de un arreglo dado.
     *
     * @param array $arr El arreglo del cual se extraerá la submatriz.
     * @param int $posStart La posición inicial desde donde se comenzará a extraer.
     * @param int $posEnd La posición final hasta donde se extraerá.
     * @return array La submatriz extraída del arreglo original.
     */
    public static function getSubArray($arr, $posStart, $posEnd)
    {
        return array_slice(HelperConvert::toArray($arr), $posStart, $posEnd);
    }

    /**
     * Obtiene el nivel de profundidad de un array multidimensional.
     *
     * @param array $array El array del cual se quiere obtener el nivel de profundidad.
     * @param int $niveles (Opcional) El nivel actual de profundidad, por defecto es -1.
     * @param array $current_array (Opcional) Array auxiliar para el cálculo de niveles, por defecto es un array vacío.
     * @return int El nivel de profundidad del array.
     */
    public static function getLevels($array, $niveles = -1, $current_array = [])
    {
        $niveles++;

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $current_array[] = self::getLevels($value, $niveles);
            }
        } else {
            return $niveles;
        }

        foreach ($current_array as $value) {
            $niveles = $value > $niveles ? $value : $niveles;
        }

        return $niveles;
    }

    /**
     * Devuelve el primer elemento de un arreglo.
     *
     * @param array $arr El arreglo del cual se obtendrá el primer elemento.
     * @return mixed El primer elemento del arreglo, o null si el arreglo está vacío.
     */
    public static function getFirstValue($arr)
    {
        return reset(HelperConvert::toArray($arr));
    }

    /**
     * Devuelve el último valor de un arreglo.
     *
     * @param array $arr El arreglo del cual se obtendrá el último valor.
     * @return mixed El último valor del arreglo, o null si el arreglo está vacío.
     */
    public static function getLastValue($arr)
    {
        $arr = HelperConvert::toArray($arr);
        return array_pop($arr);
    }

    /**
     * Obtiene las claves de un array.
     *
     * @param array $arr El array del cual se obtendrán las claves.
     * @return array Un array con las claves del array dado. Si el parámetro no es un array, se devuelve un array vacío.
     */
    public static function getKeys($arr)
    {
        // Si no es un array, devolvemos un array vacío
        if (!is_array($arr)) {
            return [];
        }

        return array_keys($arr);
    }

    /**
     * Verifica si un valor existe en un array, sin distinguir entre mayúsculas y minúsculas.
     *
     * @param array $arr El array en el que se buscará el valor.
     * @param string $value El valor a buscar en el array.
     * @return bool Devuelve true si el valor existe en el array, false en caso contrario.
     */
    public static function existsValue($arr, $value)
    {
        // Si no es un array, devolvemos false (No existe)
        if (!is_array($arr)) {
            return false;
        }

        $value = HelperString::toLower($value);

        // Como la función es sensitiva a mayúsculas i minúsculas
        // hacemos una copia de array poniendo 
        // las claves en minúsculas
        // la comparación se hace en minúsculas.
        $arrayCopy = array_map(function ($val) {
            return HelperString::toLower($val);
        }, $arr);

        return !(false === array_search($value, $arrayCopy));
    }

    /**
     * Verifica si una clave existe en un array, ignorando acentos y mayúsculas/minúsculas.
     *
     * @param array $arr El array en el que se buscará la clave.
     * @param string $key La clave que se desea buscar en el array.
     * @return bool Devuelve true si la clave existe en el array, false en caso contrario.
     */
    public static function existsKey($arr, $key)
    {
        // Si no es un array, devolvemos false (No existe)
        if (!is_array($arr)) {
            return false;
        }

        $key = HelperString::removeAccents($key);
        $key = HelperString::toLower($key);

        // Como la función array_key_exists es sensitiva 
        // a mayúsculas i minúsculas
        // hacemos una copia de array poniendo las claves 
        // en minúsculas
        // la comparación se hace en minúsculas.
        $arrayCopy = array_map(function ($val) {
            $val = HelperString::removeAccents($val);

            return HelperString::toLower($val);
        }, array_keys($arr));

        return (array_search($key, $arrayCopy) > 0);
    }

    /**
     * Compara dos arrays y devuelve los valores en el primer array que no están presentes en el segundo array.
     *
     * @param array $arr1 El primer array a comparar.
     * @param array $arr2 El segundo array a comparar.
     * @return array Un array que contiene todos los valores del primer array que no están presentes en el segundo array.
     */
    public static function dif($arr1, $arr2)
    {
        return array_diff(HelperConvert::toArray($arr1), HelperConvert::toArray($arr2));
    }

    /**
     * Elimina una columna de un array bidimensional en la posición especificada.
     *
     * @param array $array El array bidimensional del cual se eliminará la columna.
     * @param int $position La posición de la columna que se eliminará.
     * @return array El array modificado con la columna eliminada.
     */
    public static function deleteColumnByPosition($array, $position)
    {
        array_walk($array, function (&$arr) use ($position) {
            array_splice($arr, $position, 1);
        });

        return $array;
    }

    /**
     * Compacta un arreglo eliminando los elementos vacíos.
     *
     * @param array $arr El arreglo a compactar.
     * @return array El arreglo compactado sin elementos vacíos.
     */
    public static function compact($arr)
    {
        if (HelperValidate::isEmpty($arr)) {
            return [];
        }

        $arrayTmp = [];
        foreach (HelperConvert::toArray($arr) as $key => $value) {
            if (!HelperValidate::isEmpty($arr[$key])) {
                $arrayTmp[] = $value;
            }
        }

        return $arrayTmp;
    }

    /**
     * Combina dos arreglos, uno de claves y otro de valores, en un solo arreglo asociativo.
     *
     * @param array $arrayKeys Arreglo de claves.
     * @param array $arrayValues Arreglo de valores.
     * @return array Arreglo asociativo resultante de combinar las claves y los valores.
     */
    public static function combineKeysValue($arrayKeys, $arrayValues)
    {
        return array_combine(HelperConvert::toArray($arrayKeys), HelperConvert::toArray($arrayValues));
    }

    /**
     * Combina dos arreglos en uno solo.
     *
     * Esta función toma dos arreglos, los convierte a arreglos si no lo son,
     * y luego los combina en un solo arreglo utilizando array_merge.
     *
     * @param mixed $arr1 El primer arreglo o valor a combinar.
     * @param mixed $arr2 El segundo arreglo o valor a combinar.
     * @return array El arreglo combinado resultante.
     */
    public static function combine($arr1, $arr2)
    {
        return array_merge(HelperConvert::toArray($arr1), HelperConvert::toArray($arr2));
    }

    public static function pushToPos($arr, $pos, $value)
    {
        array_splice($arr, $pos, 0, [$value]);
        return $arr;
    }

    /**
     * Inseta en el inicio un valor a un array.
     *
     * Este método toma un array y un valor, y añade el valor en la primera posición del array.
     * Si el primer parámetro no es un array, se convierte a un array antes de añadir el valor.
     *
     * @param mixed $arr El array al que se le añadirá el valor. Si no es un array, se convertirá a uno.
     * @param mixed $value El valor que se añadirá en la primera posición del array.
     * @return array El array con el valor añadido en la primera posición.
     */
    public static function prepend($arr, $value)
    {
        // Nos aseguramos de que sea un array
        $arrTmp = HelperConvert::toArray($arr);

        // Añadimos en la primnera posición
        array_unshift($arrTmp, $value);

        return $arrTmp;
    }

    /**
     * Añade un valor al final de un array.
     *
     * @param mixed $arr El array al que se le añadirá el valor. Si no es un array, se convertirá en uno.
     * @param mixed $value El valor que se añadirá al final del array.
     * @return array El array con el valor añadido al final.
     */
    public static function append($arr, $value)
    {
        // Nos aseguramos de que sea un array
        $arrTmp = HelperConvert::toArray($arr);

        // Añadimos en la última posición
        array_push($arrTmp, $value);

        return $arrTmp;
    }

    /**
     * Devuelve la intersección de dos arrays basándose en sus valores.
     *
     * Esta función toma dos arrays como entrada y devuelve un array que contiene
     * los valores que están presentes en ambos arrays.
     *
     * @param array $arr1 El primer array de entrada.
     * @param array $arr2 El segundo array de entrada.
     * @return array Un array que contiene los valores que están presentes en ambos arrays.
     */
    public static function intersectionByValue($arr1, $arr2)
    {
        return array_intersect(HelperConvert::toArray($arr1), HelperConvert::toArray($arr2));
    }

    /**
     * Encuentra la intersección de dos arrays por clave y valor.
     *
     * Esta función toma dos arrays, los convierte a arrays utilizando el método
     * HelperConvert::toArray y luego encuentra la intersección de los arrays
     * resultantes, comparando tanto las claves como los valores.
     *
     * @param array $arr1 El primer array a comparar.
     * @param array $arr2 El segundo array a comparar.
     * @return array Un array que contiene todas las entradas de $arr1 que tienen
     *               claves y valores coincidentes en $arr2.
     */
    public static function intersectionByKeyValue($arr1, $arr2)
    {
        return array_intersect_assoc(HelperConvert::toArray($arr1), HelperConvert::toArray($arr2));
    }

    /**
     * Encuentra la intersección de dos arrays basándose en sus claves.
     *
     * Esta función toma dos arrays, los convierte a arrays utilizando el método
     * HelperConvert::toArray y luego devuelve un array que contiene todas las claves
     * que están presentes en ambos arrays.
     *
     * @param array $arr1 El primer array.
     * @param array $arr2 El segundo array.
     * @return array Un array que contiene todas las claves que están presentes en ambos arrays.
     */
    public static function intersectionByKey($arr1, $arr2)
    {
        return array_intersect_key(HelperConvert::toArray($arr1), HelperConvert::toArray($arr2));
    }
}
