<?php

declare (strict_types = 1);

namespace App\Controllers;

defined('ACCESS') or exit('Lo siento pero no tienes acceso aquí.');

use App\Controllers\ArrController as ArrController;

/**
 * Clase UrlController.
 * 
 * Esta clase contiene métodos útiles para trabajar con URLs.
 * 
 * @autor Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2023 Moncho Varela / Nakome <nakome@gmail.com>
 * @version 0.0.1
 */
class UrlController
{
    /**
     * C.O.R.S.
     * 
     * Este método configura las cabeceras de respuesta para permitir solicitudes CORS.
     */
    public static function cors()
    {
        // Permitir que se acceda desde cualquier origen
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decidir el origen $_SERVER['HTTP_ORIGIN']
            // que se quiere permitir, y si es así:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // caché de 1 día
        }
        // Las cabeceras de Control de Acceso se reciben durante las solicitudes de OPCIONES
        if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header('Access-Control-Allow-Methods: GET,POST, OPTIONS');
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
    }

    /**
     * Obtener IP
     *
     * Este método devuelve la dirección IP del cliente que realiza la solicitud.
     * 
     * @return string
     */
    public static function getIP(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // dirección IP desde internet compartido
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // dirección IP pasada por proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Determinar si es un error 404
     *
     * Este método determina si una URL devuelve un error 404.
     * 
     * @param string $url
     * 
     * @return bool
     */
    public static function is404(string $url): bool
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        /* Obtener el HTML o cualquier otra cosa que esté en $url. */
        $response = curl_exec($handle);
        /* Verificar si devuelve un error 404 (archivo no encontrado). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        /* Si el documento se ha cargado correctamente sin ninguna redirección o error */
        if ($httpCode >= 200 && $httpCode < 300) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Redireccionar a otra URL.
     *
     * Este método redirecciona al usuario a otra URL.
     * 
     * @param string     $url  La URL a la que se desea redireccionar
     * @param int|string $st   El código de estado HTTP que se utilizará en la respuesta de redirección
     * @param <type>     $wait Tiempo de espera antes de redireccionar, en segundos
     */
    public static function redirect($url, $st = 302)
    {
        $url = (string)$url;
        $st = (int)$st;
        $msg = [];
        $msg[301] = '301 Moved Permanently';
        $msg[302] = '302 Found';
        if (headers_sent()) {
            echo "<script>document.location.href='" . $url . "';</script>\n";
        } else {
            header('HTTP/1.1 ' . $st . ' ' . ArrController::get($msg, $st, 302));
            if (null !== $wait) {
                sleep((int)$wait);
            }
            header("Location: {$url}");
            exit(0);
        }
    }

    /**
     * Salida json
     *
     * @param array $params
     * @return void
     */
    public static function json(array $params = []):void
    {
        @header('Content-Type: application/json');
        @header('Cache-Control: private, no-cache, no-store, must-revalidate');
        @header('Pragma: no-cache');
        exit(json_encode($params, JSON_PRETTY_PRINT));
    }
}
