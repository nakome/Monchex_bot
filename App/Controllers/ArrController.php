<?php

declare (strict_types = 1);

namespace App\Controllers;

defined('ACCESS') or exit('Lo siento pero no tienes acceso aquí.');

/**
 * Clase array.
 *
 * @author    Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2016 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version 0.0.1
 */
class ArrController
{
    /**
     *  Set array.
     *
     *  @param array $array   array
     *  @param string $path   path to array
     *  @param string $value  value to array
     */
    public static function set(&$array, $path, $value):void
    {
        // Obtener segmentos del camino
        $segments = explode('.', $path);

        // Iterar a través de los segmentos
        while (count($segments) > 1) {
            $segment = array_shift($segments);

            // Si el segmento no está definido o no es un array, definirlo como array
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }

            // Obtener una referencia al segmento actual del array
            $array = &$array[$segment];
        }

        // Asignar el valor al último segmento del camino
        $array[array_shift($segments)] = $value;
    }

    /**
     *  Ordena los valores de un array.
     *
     *  @param array $a  Array a ordenar.
     *  @param array $subkey   Subclave del array a utilizar para ordenar.
     *  @param array $order  Orden de la clasificación (ASC o DESC).
     *
     *  @return value
     */
    public static function short($a, $subkey, $order = null)
    {
        // Si el array no está vacío
        if (count($a) != 0 || (!empty($a))) {
            // Recorre el array
            foreach ($a as $k => $v) {
                // Convierte el valor de la subclave a minúsculas y lo almacena en otro array
                $b[$k] = strtolower(strval($v[$subkey]));
            }
            // Si no se especificó un orden o se especificó 'ASC', ordena el array en orden ascendente
            if (null == $order || 'ASC' == $order) {
                asort($b);
            // Si se especificó 'DESC', ordena el array en orden descendente
            } elseif ('DESC' == $order) {
                arsort($b);
            }
            // Recorre el array ordenado y lo almacena en otro array en el orden original
            foreach ($b as $key => $val) {
                $c[] = $a[$key];
            }
            // Retorna el array ordenado
            return $c;
        }
    }

    /**
     * Obtener datos de un arreglo.
     *
     * @param array $array    arreglo
     * @param array $path     camino al arreglo
     * @param string $default valor por defecto si no se encuentra el camino especificado
     *
     * @return array
     */
    public static function get($array, $path, $default = null)
    {
        // Obtener segmentos del camino
        $segments = explode('.', (string)$path);
        // Recorrer los segmentos
        foreach ($segments as $segment) {
            // Verificar si el arreglo es en realidad un arreglo y si existe la clave del segmento
            if (!is_array($array) || !isset($array[$segment])) {
                return $default;
            }
            // Escribir en el arreglo
            $array = $array[$segment];
        }
        // Retornar el valor del arreglo
        return $array;
    }
}
