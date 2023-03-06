<?php

declare (strict_types = 1);

define('ROOT', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

require ROOT . '/App/defines.php';
require ROOT . '/App/autoload.php';

use App\Views\InitView as InitView;

$Init = new InitView();
$Init->run();
