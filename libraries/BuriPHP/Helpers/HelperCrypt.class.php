<?php

namespace Libraries\BuriPHP\Helpers;

use BuriPHP\Settings;

/**
 * Clase abstracta HelperCrypt
 * 
 * Esta clase proporciona métodos de cifrado y descifrado.
 * 
 * @package BuriPHP\Helpers
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.2
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 * @abstract
 */
abstract class HelperCrypt
{
    /**
     * Encripta una cadena de texto utilizando un método de transposición.
     *
     * Este método toma una cadena de texto y la encripta utilizando una técnica de transposición
     * que involucra operaciones XOR con una clave generada a partir de la longitud de la cadena
     * y la posición de cada carácter. El resultado final se codifica en base64.
     *
     * @param string $str La cadena de texto a encriptar.
     * @return string La cadena de texto encriptada y codificada en base64.
     */
    public static function encryptTransposition($str)
    {
        $len  = strlen($str);

        $strEncrypt = "";

        for ($position = 0; $position < $len; $position++) {

            $key_to_use = (($len + $position) + 1);
            $key_to_use = (255 + $key_to_use) % 255;

            $byte_to_be_encrypted = substr($str, $position, 1);

            $ascii_num_byte_to_encrypt = ord($byte_to_be_encrypted);

            $xored_byte = $ascii_num_byte_to_encrypt ^ $key_to_use;

            $encrypted_byte = chr($xored_byte);

            $strEncrypt .= $encrypted_byte;
        }

        return base64_encode($strEncrypt);
    }

    /**
     * Desencripta una cadena encriptada utilizando un método de transposición.
     *
     * @param string $strEncriptado La cadena encriptada en base64 que se va a desencriptar.
     * @return string La cadena desencriptada.
     */
    public static function decryptTransposition($strEncriptado)
    {
        $strEncriptado = base64_decode($strEncriptado);

        $len = strlen($strEncriptado);

        $str = "";

        for ($position = 0; $position < $len; $position++) {

            $key_to_use = (($len + $position) + 1);
            $key_to_use = (255 + $key_to_use) % 255;

            $byte_to_be_encrypted = substr($strEncriptado, $position, 1);

            $ascii_num_byte_to_encrypt = ord($byte_to_be_encrypted);

            $xored_byte = $ascii_num_byte_to_encrypt ^ $key_to_use;

            $encrypted_byte = chr($xored_byte);

            $str .= $encrypted_byte;
        }

        return $str;
    }

    /**
     * Encripta un mensaje utilizando una clave proporcionada.
     *
     * @param string $message El mensaje que se desea encriptar.
     * @param string $key La clave utilizada para encriptar el mensaje.
     * @return string El mensaje encriptado, codificado en base64 dos veces y con el texto invertido.
     */
    public static function encryptWithKey($message, $key)
    {
        $encryptedText = "";
        settype($message, "string");
        $i = strlen($message) - 1;
        $j = strlen($key);

        if (strlen($message) <= 0) {
            return "";
        }

        do {
            $encryptedText .= ($message[$i] ^ $key[$i % $j]);
        } while ($i--);

        $encryptedText = base64_encode(base64_encode(
            strrev($encryptedText)
        ));

        return $encryptedText;
    }

    /**
     * Desencripta un mensaje utilizando una clave proporcionada.
     *
     * @param string $message El mensaje encriptado que se va a desencriptar.
     * @param string $key La clave utilizada para desencriptar el mensaje.
     * @return string El mensaje desencriptado.
     */
    public static function decryptWithKey($message, $key)
    {
        $str = "";
        settype($message, "string");

        $message = base64_decode(base64_decode($message));
        $i       = strlen($message) - 1;
        $j       = strlen($key);

        if (strlen($message) <= 0) {
            return "";
        }

        do {
            $str .= ($message[$i] ^ $key[$i % $j]);
        } while ($i--);

        $str = strrev($str);

        return $str;
    }

    /**
     * Encripta una cadena de texto añadiendo un número aleatorio y utilizando una transposición.
     *
     * @param string $str La cadena de texto a encriptar.
     * @return string La cadena de texto encriptada con caracteres seguros para URLs.
     */
    public static function encryptRandom($str)
    {
        $numDigits = 6;
        $random     = '';
        for ($n = $numDigits; $n > 0; $n--) {
            $generated = '' . mt_rand();
            $position = mt_rand(1, strlen($generated) - 1);
            $random   .= $generated[$position];
        }

        $url = $str . '#' . $random;

        $encryptedUrl = self::encryptTransposition($url);

        // Sustituimos en el Base64 los caracteres +=/ ya que pueden 
        // dar problemas en la url
        return str_replace(
            array('+', '=', '/'),
            array('-', ',', '_'),
            $encryptedUrl
        );
    }

    /**
     * Desencripta una cadena que ha sido cifrada con un método de transposición y
     * devuelve la parte de la cadena hasta el último separador '#'.
     *
     * @param string $str La cadena cifrada que se desea desencriptar.
     * @return string La cadena desencriptada hasta el último separador '#'.
     */
    public static function decryptRandom($str)
    {
        /*  Restablecemos los caracteres +=/ */
        $encryptedUrl = str_replace(
            array('-', ',', '_'),
            array('+', '=', '/'),
            '' . $str
        );
        $url = self::decryptTransposition($encryptedUrl);

        /* Devolvemos hasta el último separador */
        return substr(
            $url,
            0,
            HelperString::indexOfLast($url, '#')
        );
    }

    /**
     * Genera una cadena de bytes aleatorios.
     *
     * Esta función intenta generar una cadena de bytes aleatorios utilizando varias fuentes de entropía.
     * Primero intenta usar `openssl_random_pseudo_bytes` si está disponible y es seguro.
     * Si no, utiliza `/dev/urandom` si está disponible, o una combinación de otras técnicas para generar entropía.
     *
     * @param int $length La longitud de la cadena de bytes aleatorios a generar. Por defecto es 16.
     * @return string Una cadena de bytes aleatorios de la longitud especificada.
     */
    public static function randomBytes($length = 16)
    {
        $sslStr = '';

        if (function_exists('openssl_random_pseudo_bytes') && (version_compare(PHP_VERSION, '5.3.4') >= 0 || constant("IS_WIN"))) {
            $sslStr = openssl_random_pseudo_bytes($length, $strong);

            if ($strong) return $sslStr;
        }

        $bitsPerRound = 2;
        $maxTimeMicro = 400;
        $shaHashLength = 20;
        $randomStr = '';
        $total = $length;

        $urandom = false;
        $handle = null;

        if (function_exists('stream_set_read_buffer') && @is_readable('/dev/urandom')) {
            $handle = @fopen('/dev/urandom', 'rb');

            if ($handle) $urandom = true;
        }

        while ($length > strlen($randomStr)) {
            $bytes = ($total > $shaHashLength) ? $shaHashLength : $total;
            $total -= $bytes;

            $entropy = rand() . uniqid(mt_rand(), true) . $sslStr;
            $entropy .= implode('', @fstat(fopen(__FILE__, 'r')));
            $entropy .= memory_get_usage();
            $sslStr = '';

            if ($urandom) {
                stream_set_read_buffer($handle, 0);
                $entropy .= @fread($handle, $bytes);
            } else {
                $samples = 3;
                $duration = 0;

                for ($pass = 0; $pass < $samples; ++$pass) {
                    $microStart = microtime(true) * 1000000;
                    $hash = sha1(mt_rand(), true);

                    for ($count = 0; $count < 50; ++$count) $hash = sha1($hash, true);

                    $microEnd = microtime(true) * 1000000;
                    $entropy .= $microStart . $microEnd;

                    if ($microStart > $microEnd) $microEnd += 1000000;

                    $duration += $microEnd - $microStart;
                }

                $duration = $duration / $samples;

                $rounds = (int) (($maxTimeMicro / $duration) * 50);

                $iter = $bytes * (int) ceil(8 / $bitsPerRound);

                for ($pass = 0; $pass < $iter; ++$pass) {
                    $microStart = microtime(true);
                    $hash = sha1(mt_rand(), true);

                    for ($count = 0; $count < $rounds; ++$count) $hash = sha1($hash, true);

                    $entropy .= $microStart . microtime(true);
                }
            }

            $randomStr .= sha1($entropy, true);
        }

        if ($urandom) @fclose($handle);

        return substr($randomStr, 0, $length);
    }

    /**
     * Crea un hash utilizando el algoritmo especificado y los datos proporcionados.
     *
     * @param string $algorithm El algoritmo de hash a utilizar (por ejemplo, 'sha256').
     * @param string $data Los datos que se van a hashear.
     * @return string El hash resultante.
     */
    public static function createHash($algorithm, $data)
    {
        $context = hash_init($algorithm, HASH_HMAC, Settings::$secret);
        hash_update($context, $data);

        return hash_final($context);
    }

    /**
     * Crea una contraseña segura utilizando un hash y un salt aleatorio.
     *
     * @param string $string La cadena de texto que se utilizará para generar la contraseña.
     * @return string La contraseña generada en el formato 'hash:salt'.
     */
    public static function createPassword($string)
    {
        $salt = HelperString::random(64);
        $password = self::createHash('sha1', $string . $salt);

        return $password . ':' . $salt;
    }
}
