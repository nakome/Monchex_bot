<?php 
// Define el acceso al script
define('ACCESS', true);
// Debugear ?
define('DEBUG',true);

// Mostrar errores si dev_mode es true
if (DEBUG) {
    ini_set('display_errors', (string) 1);
    ini_set('display_startup_errors', (string) 1);
    ini_set('track_errors', (string) 1);
    ini_set('html_errors', (string) 1);
    error_reporting(E_ALL | E_STRICT | E_NOTICE);
} else {
    ini_set('display_errors', (string) 0);
    ini_set('display_startup_errors', (string) 0);
    ini_set('track_errors', (string) 0);
    ini_set('html_errors', (string) 0);
    error_reporting(0);
}

// Dar formato a la fecha
setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
// Cambia el valor de ..
header("X-Powered-By: App");
// Comprueba la version de php
if (version_compare($ver = PHP_VERSION, $req = '7.4.0', '<')) {
    $out = sprintf('Usted esta usando PHP %s, pero aplicación necesita <strong>PHP %s</strong> para funcionar.', $ver, $req);
    exit($out);
}