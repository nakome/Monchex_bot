<?php

/*
 * Declara al principio del archivo, las llamadas a las funciones respetarán
 * estrictamente los indicios de tipo (no se lanzarán a otro tipo).
 */
declare (strict_types = 1);

namespace App\Views;

use App\Controllers\RouterController as RouterController;
use App\Controllers\RouteController as RouteController;

/**
 * Clase InitView.
 *
 * @autor      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright  2023 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version    0.0.1
 */
class InitView
{
    /**
     * Contructor
     */
    public function __construct()
    {
        // Se instancian los objetos necesarios para el funcionamiento de la vista.
        $this->routerController = new RouterController();
        $this->routeController = new RouteController();
    }

    /**
     * Rutas.
     */
    public function routes()
    {
        $rutas = [
            '/', // Ruta principal.
            '/(:any)', // Ruta que acepta cualquier cadena de caracteres.
            '/(:any)/(:any)', // Ruta que acepta dos cadenas de caracteres.
        ];
        // Se establecen las rutas definidas en el modelo y se asigna una función anónima
        // que ejecutará la vista correspondiente cuando se invoque esa ruta.
        $this->routerController->route($rutas,
            fn(
                string $type = "",
                string $method = ""
            ) => $this->routeController->run($type, $method)
        );
    }

    public function run()
    {
        // Se establecen las rutas y se lanza el controlador del router.
        $this->routes();
        $this->routerController->launch();
    }
}
