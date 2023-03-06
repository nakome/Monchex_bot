<?php

/*
 * Declara al principio del archivo, las llamadas a las funciones respetarán
 * estrictamente los indicios de tipo (no se lanzarán a otro tipo).
 */
declare (strict_types = 1);

namespace App\Controllers;

use App\Controllers\ApiController as ApiController;
use App\Controllers\SessionController as SessionController;
use App\Controllers\UrlController as UrlController;
use App\Controllers\UtilsController as UtilsController;
use App\Views\RouterView as RouterView;

/**
 * Clase Route controller.
 *
 * @autor      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright  2023 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version    0.0.1
 */
class RouteController
{
    /**
     * Contructor
     */
    public function __construct()
    {
        $this->apiController = new ApiController();
        $this->utilsController = new UtilsController();
        $this->routerView = new RouterView();
    }

    /**
     * Run router
     *
     * @param string $type
     * @return void
     */
    public function run(string $type = "")
    {
        // Llama al método static cors() del controlador UrlController
        UrlController::cors();

        // Iniciamos la session
        SessionController::start();

        switch ($type) {
            case 'check_authorization':
                $this->routerView->check_authorization();
                break;
            case 'logout':
                $this->routerView->logout();
                break;
            case 'msg':
                $this->routerView->getMessageFromTelegram();
                break;
            default:
                $output = "";
                // Obtener los datos del usuario de Telegram
                $tg_user = $this->apiController->getTelegramUserData();
                // Verificar si se obtuvieron los datos del usuario correctamente
                if (false !== $tg_user) {
                    // plantilla para autorizados
                    $output = $this->routerView->templateFromAuth($tg_user);
                } else {
                    // plantilla para no autorizados
                    $output = $this->routerView->templateFromNoAuth($tg_user);
                }
                // plantilla general
                die($this->routerView->templateRoot($output));
                break;
        }
    }
}
