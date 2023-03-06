<?php

declare (strict_types = 1);

namespace App\Controllers;

defined('ACCESS') or exit('Lo siento pero no tienes acceso aquí.');

use App\Controllers\DotEnvController as DotEnvController;
use App\Controllers\UrlController as UrlController;

/**
 * Clase Controlador de Rutas.
 *
 * @autor      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright  2020 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version    0.0.1
 */
class RouterController
{
    /**
     * Rutas definidas.
     *
     * @var array
     */
    private $__routes = [];

    /**
     * Define una ruta.
     *
     * @param array $patterns   patrones de ruta
     * @param callable $callback  función controladora de la ruta
     */
    public function route(
        array $patterns,
        callable $callback
    ): void {
        // si no es un array, lo convierte en uno
        if (!is_array($patterns)) {
            $patterns = array($patterns);
        }

        // crea las rutas a partir de los patrones
        foreach ($patterns as $pattern) {
            // quita los caracteres '/' al inicio y al final del patrón
            $pattern = trim($pattern, '/');

            // reemplaza las cadenas de texto especiales del patrón por expresiones regulares
            $pattern = str_replace(
                array('\(', '\)', '\|', '\:any', '\:num', '\:all', '#'),
                array('(', ')', '|', '[^/]+', '\d+', '.*?', '\#'),
                preg_quote($pattern, '/')
            );

            // agrega el patrón y la función controladora a la lista de rutas
            $this->__routes['#^' . $pattern . '$#'] = $callback;
        }
    }

    /**
     * Lanza la ruta correspondiente a la URL actual.
     */
    public function launch()
    {
        // carga el archivo .env para obtener las variables de entorno
        (new DotEnvController(ROOT . '/.env'))->load();

        // activa el almacenamiento en buffer de salida
        ob_start();

        // obtiene la URL actual
        $url = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '';

        // obtiene la base de la URL actual
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if (strpos((string)$url, $base) === 0) {
            $url = substr($url, strlen($base));
        }

        // quita los caracteres '/' al inicio y al final de la URL actual
        $url = trim((string)$url, '/');

        // busca la ruta correspondiente a la URL actual
        foreach ($this->__routes as $pattern => $callback) {
            if (preg_match($pattern, $url, $params)) {
                array_shift($params);
                // devuelve el resultado de la función controladora de la ruta
                return call_user_func_array($callback, array_values($params));
            }
        }

        // si no se encuentra una ruta, devuelve un error 404
        if (UrlController::is404($url)) {
            @header('Content-type: application/json');
            $arr = array(
                'STATUS' => 404,
                'HTTP_HOST' => array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '',
                'REQUEST_METHOD' => array_key_exists('REQUEST_METHOD', $_SERVER) ? $_SERVER['REQUEST_METHOD'] : '',
                'OPTS' => $_GET,
            );
            exit(json_encode($arr));
        }

        // finaliza
        ob_end_flush();
        exit;
    }
}
