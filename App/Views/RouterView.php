<?php

/*
 * Declara al principio del archivo, las llamadas a las funciones respetarán
 * estrictamente los indicios de tipo (no se lanzarán a otro tipo).
 */
declare (strict_types = 1);

namespace App\Views;


use App\Controllers\ApiController as ApiController;
use App\Controllers\UrlController as UrlController;

/**
 * Clase Router view.
 *
 * @autor      Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright  2023 Moncho Varela / Nakome <nakome@gmail.com>
 *
 * @version    0.0.1
 */
class RouterView
{
    /**
     * Contructor
     */
    public function __construct()
    {
        $this->apiController = new ApiController();
    }

    /**
     * Run router
     *
     * @param string $type
     * @return void
     */
    public function check_authorization(string $type = "")
    {
        try {
            // Verifica los datos de autenticación de Telegram enviados mediante el parámetro GET
            $auth_data = $this->apiController->checkTelegramAuthorization($_GET);
            // Almacena los datos de autenticación en una cookie para ser utilizados en sesiones posteriores
            $this->apiController->saveTelegramUserData($auth_data);
        } catch (Exception $e) {
            // En caso de error, muestra el mensaje de excepción y termina la ejecución del script
            die($e->getMessage());
        }
        // volvemos al inicio de la pagina
        UrlController::redirect(getenv('URL'));
    }

    /**
     * Salir de telegram
     *
     * @return void
     */
    public function logout()
    {
        $this->apiController->logout();
    }

    /**
     * Obtener mensaje de Telegram
     *
     * @return void
     */
    public function getMessageFromTelegram()
    {
        $this->apiController->retriveDataMessage();
    }

    /**
     * Plantilla para autorizados
     *
     * @param array $tg_user
     * @return string
     */
    public function templateFromAuth(array $tg_user = []): string
    {
        // Obtener la URL del sitio desde las variables de entorno
        $site_url = getenv('URL');
        $botname = getenv('BOT_USERNAME');

        // Obtener el nombre y apellido del usuario
        $first_name = htmlspecialchars($tg_user['first_name']);
        $last_name = htmlspecialchars($tg_user['last_name']);

        // Verificar si el usuario tiene un nombre de usuario de Telegram
        if (isset($tg_user['username'])) {
            $username = htmlspecialchars($tg_user['username']);
            // Crear una etiqueta de anclaje para el nombre de usuario
            $html = "<h1>Hola, <a href=\"https://t.me/{$username}\">{$first_name} {$last_name}</a>!</h1>";
        } else {
            // Si el usuario no tiene un nombre de usuario, simplemente mostrar su nombre completo
            $html = "<h1>Hola, {$first_name} {$last_name}!</h1>";
        }

        // Verificar si el usuario tiene una foto de perfil de Telegram
        if (isset($tg_user['photo_url'])) {
            $photo_url = htmlspecialchars($tg_user['photo_url']);
            // Agregar la imagen de perfil al HTML
            $html .= "<img src=\"{$photo_url}\">";
        }

        // Agregar un enlace de cierre de sesión al HTML
        $html .= "<p><a class='logout' href=\"{$site_url}/logout\">Cerrar sesión</a></p>";

        return $html;
    }

    /**
     * Plantilla para no autorizados
     *
     * @return string
     */
    public function templateFromNoAuth():string
    {
        // Obtener la URL del sitio desde las variables de entorno
        $site_url = getenv('URL');
        $botname = getenv('BOT_USERNAME');
        // Si no se pudo obtener los datos del usuario de Telegram, mostrar un widget de inicio de sesión de Telegram
        $bot_username = $botname;
        return <<<HTML
            <h1>Hola, anónimo!</h1>
            <script async src="https://telegram.org/js/telegram-widget.js?21" data-telegram-login="{$botname}" data-size="large" data-radius="4" data-auth-url="{$site_url}/check_authorization/" data-request-access="write"></script>
        HTML;
    }

    /**
     * Plantilla por defecto
     *
     * @param string $html
     * @return void
     */
    public function templateRoot(string $html = ""): string
    {
        $css = <<<CSS
            :root{
                --color-1: #0d2348;
                --color-2: #5783d0;
                --color-3: #fefae4;
                --color-4: #2b2814;
                --font-family: system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif,Roboto,Arial,sans-serif;
                --font-size: 14px;
                --font-weight: 400;
                --line-height: 1.6;
                --shadow: 5px 5px 0px #333333;
            }
            *{
                box-sizing:border-box;
            }
            html,body{
                position:relative;
                height:100%;
            }
            body,
            html {
                position: relative;
                height: 100%;
            }
            body {
                height: 100%;
                background: var(--color-1);
                padding: 0;
                font-family: var(--font-family);
                font-size: var(--font-size);
                font-weight: var(--font-weight);
                line-height: var(--line-height);
                color:var(--color-3);
                overflow: hidden;
                margin: 0;
            }
            img,
            svg {
                max-width: 100%;
                vertical-align: text-top;
            }
            a {
                opacity:1;
                color: var(--color-3);
            }
            a:hover,a:focus{
                opacity:0.5;
            }
            .logout {
                background-color: var(--color-2);
                border: 2px solid var(--color-3);
                color: var(--color-3);
                padding: 5px 10px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: var(--font-size);
                margin: 0 2px;
                cursor: pointer;
                border-radius: 4px;
                opacity:1;
                transition:opacity 500ms ease;
            }
            .logout:hover {
                opacity:0.5;
                transition:opacity 500ms ease;
            }
            main{
                margin:10px;
            }
            main>header{
                max-width:300px;
                margin:10px auto;
            }
            main>section{
                max-width:300px;
                margin:10px auto;
            }
            h1,h2,h3,h4,h5,h6{color:var(--color-2);}
        CSS;

        $javascript = <<<JAVASCRIPT
            console.log('ready');
        JAVASCRIPT;

        // Imprimir el HTML completo con la respuesta HTTP
        return <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Monchex bot</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style rel="stylesheet">{$css}</style>
            </head>
                <body>
                    <main id="main">
                        <header><img src="./logo_bot.png" alt="logo"/></header>
                        <section id="content">{$html}</section>
                    </main>
                    <script rel="javascript">{$javascript}</script>
                </body>
            </html>
        HTML;
    }
}
