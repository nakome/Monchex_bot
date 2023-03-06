<?php

declare (strict_types = 1);

namespace App\Controllers;

defined('ACCESS') or exit('Lo siento pero no tienes acceso aquí.');

/**
 * Clase DotEnv.
 * Carga variables de entorno desde un archivo .env.
 *
 * @autor Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2023 Moncho Varela / Nakome <nakome@gmail.com>
 * @version 0.0.1
 */
class DotEnvController
{
    /**
     * Directorio donde se encuentra el archivo .env.
     *
     * @var string
     */
    protected $_path;

    /**
     * Constructor.
     *
     * @param string $path Ruta al archivo .env.
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s no existe', $path));
        }
        $this->_path = $path;
    }

    /**
     * Carga las variables de entorno.
     */
    public function load(): void
    {
        if (!is_readable($this->_path)) {
            throw new \RuntimeException(sprintf('El archivo %s no es legible', $this->_path));
        }

        // Carga las líneas del archivo .env en un array
        $lines = file($this->_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Recorre el array y carga las variables de entorno
        foreach ($lines as $line) {

            // Si la línea empieza por #, es un comentario y se ignora
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Divide la línea en nombre y valor separados por =
            list($nombre, $valor) = explode('=', $line, 2);

            // Elimina espacios en blanco y comillas de las variables
            $nombre = trim($nombre);
            $valor = trim($valor, " \t\n\r\0\x0B\"'");

            // Si la variable de entorno no está definida, la define y le asigna el valor
            if (!array_key_exists($nombre, $_SERVER) && !array_key_exists($nombre, $_ENV)) {
                putenv(sprintf('%s=%s', $nombre, $valor));
                $_ENV[$nombre] = $valor;
                $_SERVER[$nombre] = $valor;
            }
        }
    }
}
