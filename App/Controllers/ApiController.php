<?php

/*
 * Declara al principio del archivo, las llamadas a las funciones respetarán
 * estrictamente los indicios de tipo (no se lanzarán a otro tipo).
 */
declare (strict_types = 1);

namespace App\Controllers;

use App\Controllers\SessionController as SessionController;
use App\Controllers\UrlController as UrlController;
use App\Controllers\UtilsController as UtilsController;

/**
 * Clase Api.
 *
 * @autor Moncho Varela / Nakome <nakome@gmail.com>
 * @copyright 2023 Moncho Varela / Nakome <nakome@gmail.com>
 * @version 0.0.1
 */
class ApiController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Constructor vacío
        $this->utilsController = new UtilsController();
    }

    /**
     * Salir de Telegram
     *
     * @return void
     */
    public function logout()
    {
        // Si la cookie existe, establecer su valor en una cadena vacía para eliminarla
        setcookie('tg_user', '');
        // Borramos la session
        SessionController::delete('tg_user');
        // destruye la session
        SessionController::destroy();
        // Redirigir al usuario a la página de inicio
        UrlController::redirect(getenv('URL'));
    }

    /**
     * La función getTelegramUserData() es una función de PHP que devuelve los datos de autenticación del usuario
     * de Telegram en forma de arreglo asociativo si se encuentra almacenado en una cookie llamada 'tg_user'.
     * Si no se encuentra almacenado, devuelve false.
     *
     * @return void
     */
    public function getTelegramUserData()
    {
        if (SessionController::exists('tg_user')) {
            $auth_data_json = urldecode(SessionController::get('tg_user'));
            $auth_data = json_decode($auth_data_json, true);
            return $auth_data;
        }
        return false;
    }

    /**
     * Comprueba la autorización de telegram
     *
     * @param array $auth_data
     * @return void
     */
    public function checkTelegramAuthorization($auth_data)
    {
        // Obtiene el hash de verificación de los datos de autenticación
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);

        // Crea una cadena de texto con los datos de autenticación ordenados alfabéticamente
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);

        // Genera la clave secreta utilizando el token de acceso de tu bot de Telegram
        $secret_key = hash('sha256', BOT_TOKEN, true);

        // Genera el hash HMAC de los datos de autenticación y la clave secreta
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        // Verifica que el hash HMAC generado coincide con el hash de verificación proporcionado
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('Los datos no son de Telegram');
        }

        // Verifica que los datos de autenticación no hayan caducado (24 horas)
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Los datos estar desactualizados');
        }

        // Devuelve los datos de autenticación verificados
        return $auth_data;
    }

    /**
     * Guardar datos de usuario
     *
     * @param [type] $auth_data
     * @return void
     */
    public function saveTelegramUserData($auth_data)
    {
        // Codifica los datos de autenticación en formato JSON
        $auth_data_json = json_encode($auth_data);
        // Establece una cookie con los datos de autenticación codificados en JSON
        setcookie('tg_user', $auth_data_json);
        // Set session
        SessionController::set('tg_user', $auth_data_json);
    }

    /**
     * Obtener mensajes desde telegram
     *
     * https://api.telegram.org/bot{TOKEN}/setWebhook?url={WEBHOOKURL}
     *
     * @return void
     */
    public function retriveDataMessage()
    {
        $update = json_decode(file_get_contents('php://input'), true);
        if ($update) {
            // Escribimos en el log
            $this->utilsController->log('Obtiene datos de telegram', ($update) ? json_encode($update) : "Peticion a Telegram");
            // Check for normal command
            $msg = $update['message']['text'];
            switch ($msg) {
                case '/start':
                    $msg = "Hola soy un bot que ayuda a los desarrolladores :).";
                    $this->sendMsg($update, $msg);
                    break;
                case '/help':
                    $msg = "Puedes usar los comandos desde el menu.\n\nrandom_password - Genera un password aleatorio\nhtml5 - Genera una plantilla base\nlorem_sm - Genera un parrafo de lorem\nlorem_md - Genera parrafos de lorem\nlorem_xl - Genera parrafos de lorem largos\nhtml_ul - Genera una lista\nhtml_ol - Genera una lista ordenada\nhtml_form - Genera una formulario\nhtml_table - Genera una tabla\nlorem_pixel - Genera una imagen aleatoria";
                    $this->sendMsg($update, $msg);
                    break;
                case '/lorem_sm':
                    $msg = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget magna non lectus hendrerit posuere. Nam at tempus purus, ut interdum dui. Ut rhoncus rutrum posuere. Cras tellus dolor, auctor non interdum eu, venenatis nec arcu. Vivamus eleifend lacus ut interdum elementum. Curabitur a aliquet massa. Mauris ut mollis odio. Sed posuere, nibh a feugiat tempor, orci enim accumsan magna, nec tempus elit lorem quis ante. Donec mollis orci eu metus sodales porta. Mauris ex sapien, interdum ac lorem sit amet, mattis sollicitudin ligula. Etiam malesuada, lorem at facilisis accumsan, ipsum massa euismod massa, ut sagittis erat lorem eget purus. Mauris sodales at nisi nec dignissim. Nunc tincidunt, nibh at mattis mattis, magna diam cursus dui, a facilisis nisl purus et lectus. Nullam id malesuada magna. In iaculis bibendum ex ut sodales. Sed cursus enim et imperdiet feugiat.";
                    $this->sendMsg($update, $msg);
                    break;
                case '/lorem_md':
                    $msg = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi venenatis, dui sit amet porttitor tincidunt, mi lorem bibendum eros, ultricies fermentum massa nisi at purus. Duis a auctor lectus. Integer tristique, est sit amet sagittis efficitur, tellus justo tristique leo, at scelerisque velit tortor sed enim. Etiam eros nunc, placerat quis nulla eu, semper vulputate tortor. Duis et arcu ut metus feugiat faucibus a at orci. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel scelerisque elit. Duis ultrices lobortis blandit. Morbi a nisl ipsum. In a libero nec eros blandit auctor non et diam.\n\n";
                    $msg .= "Duis blandit vel enim quis eleifend. Integer est felis, volutpat non odio eget, gravida gravida leo. Sed velit sem, viverra non laoreet quis, vestibulum a mi. Quisque dapibus leo eu sapien luctus cursus. Integer mattis ipsum id urna volutpat auctor. Duis aliquam, purus vel lobortis auctor, sapien est aliquet velit, eget mattis est arcu et ex. Pellentesque elementum mattis erat, quis euismod erat fermentum eget.";
                    $this->sendMsg($update, $msg);
                    break;
                case '/lorem_xl':
                    $msg = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur mauris massa, fringilla vel sem eget, aliquam pulvinar augue. Aliquam posuere, augue quis cursus sollicitudin, erat dolor posuere purus, ut tincidunt sapien nisl in massa. Nullam sit amet mauris dolor. Donec feugiat urna eu eros ornare, eu imperdiet nibh egestas. Praesent tempor vel urna in elementum. Pellentesque dictum nisl arcu, a fermentum justo aliquam vitae. Suspendisse suscipit nec velit ac consequat. Nulla a vulputate felis. Integer ac condimentum ante. Vivamus id enim lacinia, pretium magna a, vestibulum eros. Nullam ut maximus tellus. Sed vehicula urna ut turpis auctor congue.\n\n";
                    $msg .= "Ut molestie felis massa, sit amet scelerisque velit varius efficitur. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nulla imperdiet aliquet vulputate. Maecenas id egestas magna. Mauris consectetur ultrices metus eget laoreet. Aenean scelerisque commodo mi, ac tempor lectus sodales sed. Ut ultricies rutrum mattis. Cras ultrices sit amet enim ultricies tincidunt. Nunc varius nibh sem, quis ullamcorper sem rutrum eget. Aenean ut libero at velit placerat pharetra vitae quis ipsum. Cras eget elit elementum, gravida sem vel, lobortis arcu. Duis facilisis vestibulum sapien non interdum. Cras ut massa ac neque interdum consectetur sit amet sit amet erat. Aliquam erat volutpat. Maecenas in tortor at neque vulputate consectetur. Nunc vel nulla justo.\n\n";
                    $msg .= "Proin ullamcorper sem ante, auctor semper metus aliquam non. Pellentesque id elementum elit. In sollicitudin neque nec metus commodo posuere. Donec eu pretium velit. Aenean eu urna eros. Pellentesque ornare purus et suscipit venenatis. Integer vitae faucibus tortor. Ut tempor in ipsum vitae sodales. Donec sodales condimentum arcu ut mollis. Etiam dapibus varius erat, eget imperdiet lacus fringilla sed.";
                    $this->sendMsg($update, $msg);
                    break;
                case '/html_ul':
                    $msg = <<<HTML
                    <ul>
                        <li>List item 1</li>
                        <li>List item 2</li>
                        <li>List item 3</li>
                    </ul>
                    HTML;
                    $this->sendMsg($update, $msg);
                    break;
                case '/html_ol':
                    $msg = <<<HTML
                    <ol>
                        <li>List item 1</li>
                        <li>List item 2</li>
                        <li>List item 3</li>
                    </ol>
                    HTML;
                    $this->sendMsg($update, $msg);
                    break;
                case '/html_form':
                    $msg = <<<HTML
                    <form action="/action_page.php">
                        <label for="fname">First name:</label><br>
                        <input type="text" id="fname" name="fname" value="John"><br>
                        <label for="lname">Last name:</label><br>
                        <input type="text" id="lname" name="lname" value="Doe"><br><br>
                        <label for="lemail">Email:</label><br>
                        <input type="email" id="lemail" name="lemail" value="mail@mail.com"><br><br>
                        <label for="cars">Choose a car:</label><br><br>
                        <select id="subject" name="subject"><br><br>
                            <option value="one">One</option>
                            <option value="two">Two</option>
                            <option value="three">Three</option>
                        </select>
                        <textarea name="message" rows="10" cols="30">The cat was playing in the garden.</textarea><br><br>
                        <input type="submit" value="Submit">
                    </form>
                    HTML;
                    $this->sendMsg($update, $msg);
                    break;
                case '/html_table':
                    $msg = <<<HTML
                    <table>
                    <tr>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Country</th>
                    </tr>
                    <tr>
                        <td>Alfreds Futterkiste</td>
                        <td>Maria Anders</td>
                        <td>Germany</td>
                    </tr>
                    <tr>
                        <td>Centro comercial Moctezuma</td>
                        <td>Francisco Chang</td>
                        <td>Mexico</td>
                    </tr>
                    <tr>
                        <td>Ernst Handel</td>
                        <td>Roland Mendel</td>
                        <td>Austria</td>
                    </tr>
                    <tr>
                        <td>Island Trading</td>
                        <td>Helen Bennett</td>
                        <td>UK</td>
                    </tr>
                    <tr>
                        <td>Laughing Bacchus Winecellars</td>
                        <td>Yoshi Tannamuri</td>
                        <td>Canada</td>
                    </tr>
                    <tr>
                        <td>Magazzini Alimentari Riuniti</td>
                        <td>Giovanni Rovelli</td>
                        <td>Italy</td>
                    </tr>
                    </table>
                    HTML;
                    $this->sendMsg($update, $msg);
                    break;
                case '/lorem_pixel':
                    $msg = "https://picsum.photos/1024/768";
                    $this->sendMsg($update, $msg);
                    break;
                case '/random_password':
                    $password = $this->utilsController->generatePassword();
                    $msg = "El nuevo password es {$password}";
                    $this->sendMsg($update, $msg);
                    break;
                case '/html5':
                    $code = <<<HTML
                    <!DOCTYPE html>
                    <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <meta http-equiv="X-UA-Compatible" content="ie=edge">
                            <title>Mi Página Web</title>
                        </head>
                        <body>
                            <header>
                                <h1>Mi Página Web</h1>
                                <nav>
                                <ul>
                                    <li><a href="#">Inicio</a></li>
                                    <li><a href="#">Acerca de</a></li>
                                    <li><a href="#">Contacto</a></li>
                                </ul>
                                </nav>
                            </header>
                            <main>
                                <h2>Bienvenidos a mi página web</h2>
                                <p>¡Hola a todos! En esta página podrás encontrar información sobre mis proyectos y hobbies.</p>
                            </main>
                            <footer>
                                <p>© 2023 Mi Página Web</p>
                            </footer>
                        </body>
                    </html>
                    HTML;
                    $this->sendMsg($update, $code);
                    break;
                case '/random_color':
                    $msg = $this->utilsController->getRandomHexColor();
                    $this->sendMsg($update,"El nuevo color es {$msg}");
                    break;
                default:
                    $msg = "Yo no entender lo que tu decir :). Por favor escribe /help para ver los comandos.";
                    $this->sendMsg($update, $msg);
                    break;
            }
        }
    }

    /**
     * Mandamos mensaje a Telegram
     *
     * @param array $update  Información del mensaje que se va a responder.
     * @param string $msg    Mensaje que se va a enviar al usuario.
     * @return void
     */
    public function sendMsg(array $update = [], string $msg = ""): void
    {
        // Obtenemos el token del bot desde la variable de entorno.
        $botToken = getenv('BOT_TOKEN');

        // Construimos la URL de la API de Telegram utilizando el token del bot.
        $botAPI = "https://api.telegram.org/bot" . $botToken;

        // Creamos un array con los datos del mensaje que se va a enviar.
        $data = http_build_query([
            'text' => $msg, // El texto del mensaje.
            'chat_id' => $update['message']['from']['id'], // El ID del chat al que se enviará el mensaje.
        ]);

        // Enviamos el mensaje utilizando la API de Telegram.
        file_get_contents($botAPI . "/sendMessage?{$data}");

        // Finalizamos el script.
        exit();
    }
}
