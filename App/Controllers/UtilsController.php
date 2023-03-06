<?php

/*
 * Declara al principio del archivo, las llamadas a las funciones respetarán
 * estrictamente los indicios de tipo (no se lanzarán a otro tipo).
 */
declare (strict_types = 1);

namespace App\Controllers;

/**
 * Clase Utils Controller.
 *
 * @autor Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2023 Moncho Varela / Nakome <nakome@gmail.com>
 * @version 0.0.1
 */
class UtilsController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Constructor vacío
    }

    /**
     * Genera una contraseña aleatoria con una longitud dada.
     * @param int $length La longitud de la contraseña que se desea generar. Por defecto es 10.
     * @return string La contraseña generada.
     */
    public function generatePassword($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $password .= substr($characters, $index, 1);
        }
        return $password;
    }

    /**
     * Genera un color aleatorio en formato hexadecimal.
     *
     * @return string Color en formato hexadecimal.
     */
    public function getRandomHexColor()
    {
        $letters = '0123456789ABCDEF'; // Letras y números permitidos en un color hexadecimal.
        $color = '#'; // Inicializamos el valor del color con el símbolo de #.
        for ($i = 0; $i < 6; $i++) {
            $color .= $letters[rand(0, 15)]; // Agregamos un caracter aleatorio al color.
        }
        return $color; // Devolvemos el color generado.
    }

    /**
     * Log files
     *
     * Esta función se utiliza para registrar información en un archivo de registro.
     * Recibe dos parámetros: el nombre del archivo y los datos que se van a registrar.
     *
     * @param string $name El nombre del archivo de registro.
     * @param string $data Los datos que se van a registrar en el archivo.
     *
     * @return void
     */
    public function log(string $name, string $data): void
    {
        $logFile = ROOT . '/log.txt';

        // Si la configuración de DEBUG está activa, se abre el archivo de registro y se escribe la información.
        if (DEBUG) {
            $fileOpen = fopen($logFile, 'a+') or die("Can't open file.");
            $body = "\n == {$name}\n";
            $body .= $data . " - " . date('d/m/Y H:m:s');
            fwrite($fileOpen, $body);
            fclose($fileOpen);
        }

        // Si la configuración de DEBUG no está activa, se elimina el archivo de registro si existe.
        if (!DEBUG) {
            if (file_exists($logFile) && is_file($logFile)) {
                unlink($logFile);
            }
        }
    }
}
