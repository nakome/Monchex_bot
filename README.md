# Configuración de ```.env```

La idea de crear un bot de Telegram surge de lo de siempre, a la hora de buscar por ejemplo un texto lorem tengo que abrir la página buscar el enlace y copiarlo y ahora con el comando ```/lorem_sm[md][xl]``` ya lo tengo, o un password, o una plantilla básica de html5. Aparte de esto támbien fue por curiosidad de como funcionan los bots de Telegram y intentare actualizarlo cuando me sea posible.

El código Php no usa librerias externas esta todo hecho por mi y he intentado que sea lo mas fácil de comprender posible ademas de que pueda ser extensible.

**Configuración de .env:** _Las constantes de env se leen con ```getenv('CONSTANTE')```.

```
URL=[url de la web]
# Api url de telegram
API_URL=https://api.telegram.org/bot
# Token de telegram
BOT_TOKEN=[url de el bot de Telegram]
# Nombre del bot
BOT_USERNAME=[Nombre de el bot]
```

### Configuración de envio de mensajes

**Url del webhook:**

    TOKEN = BOT_TOKEN
    WEBHOOKURL = [Url de la web]/msg
    https://api.telegram.org/bot{TOKEN}/setWebhook?url={WEBHOOKURL}



### Comandos

los comandos son muy básicos por ahora y son los siguientes.

```
Puedes usar los comandos desde el menu.
random_password - Genera un password aleatorio
random_color - Genera un color aleatorio
html5 - Genera una plantilla base
lorem_sm - Genera un parrafo de lorem
lorem_md - Genera parrafos de lorem
lorem_xl - Genera parrafos de lorem largos
html_ul - Genera una lista
html_ol - Genera una lista ordenada
html_form - Genera una formulario
html_table - Genera una tabla
lorem_pixel - Genera una imagen aleatoria
```

### Añadir un nuevo comando

Para crear otro mensaje
```
// App\Controllers\ApiController linea 254
case '/loquesea':
    $msg = "Mensaje de vuelta del comando";
    $this->sendMsg($update, $msg);
    break;
```