<?php

declare (strict_types = 1);

namespace App\Controllers;

defined('ACCESS') or exit('Lo siento pero no tienes acceso aquí.');

/**
 * Clase Session.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 */
class SessionController
{
    /**
     *  Iniciar sesión.
     *
     * @return bool
     */
    public static function start(): bool
    {
        // ¿La sesión ya está iniciada?
        if (!session_id()) {
            // Iniciar sesión
            return @session_start();
        }
        // Si ya está iniciada
        return true;
    }

    /**
     *  Eliminar sesión.
     */
    public static function delete()
    {
        // Recorrer todos los argumentos
        foreach (func_get_args() as $argument) {
            // Elemento de array
            if (is_array($argument)) {
                // Recorrer las claves
                foreach ($argument as $key) {
                    // Desestablecer la clave de sesión
                    unset($_SESSION[(string)$key]);
                }
            } else {
                // Eliminar del array
                unset($_SESSION[(string)$argument]);
            }
        }
    }

    /**
     *  Destruir sesión.
     */
    public static function destroy()
    {
        // Destruir
        if (session_id()) {
            session_unset();
            session_destroy();
            $_SESSION = [];
        }
    }

    /**
     *  Verificar sesión.
     */
    public static function exists()
    {
        // Iniciar sesión si es necesario
        if (!session_id()) {
            self::start();
        }
        // Recorrer todos los argumentos
        foreach (func_get_args() as $argument) {
            // Elemento de array
            if (is_array($argument)) {
                // Recorrer las claves
                foreach ($argument as $key) {
                    // NO existe
                    if (!isset($_SESSION[(string)$key])) {
                        return false;
                    }
                }
            } else {
                // NO existe
                if (!isset($_SESSION[(string)$argument])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *  Obtener sesión.
     */
    public static function get($key)
    {
        // Iniciar sesión si es necesario
        if (!session_id()) {
            self::start();
        }
        // Redefinir clave
        $key = (string)$key;
        // Obtener clave
        if (self::exists((string)$key)) {
            return $_SESSION[(string)$key];
        }
        // La clave no existe
        return;
    }

    /**
     *  Establecer sesión.
     *
     *  @param  string $key   clave
     *  @param  string $value   valor
     */
    public static function set($key, $value)
    {
        // Iniciar sesión si es necesario
        if (!session_id()) {
            self::start();
        }
        // Establecer clave
        $_SESSION[(string)$key] = $value;
    }
}
