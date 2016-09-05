<?php
date_default_timezone_set('Europe/Kiev');

header('Content-Type: text/html; charset=utf-8');

define('APPLICATION_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development');
define('BASE_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('APPLICATION_MODELS_PATH', BASE_PATH . '/application/models');

set_include_path(implode(PATH_SEPARATOR, array(
    BASE_PATH,
    APPLICATION_PATH,
    APPLICATION_MODELS_PATH,
    get_include_path()
)));

require_once BASE_PATH . '/vendor/autoload.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

if ($_SERVER['REQUEST_URI'] === '/p/' && APPLICATION_ENV === 'development') {
    echo '<!DOCTYPE html>'
            . '<html lang="ru">'
                . '<head><meta charset="UTF-8"><title></title></head>'
                . '<body><a href="/p/ru">go</a></body>'
            . '</html>';
} else {
    try {
        $application
            ->bootstrap()
            ->run();
    } catch (\Exception $e){
        var_dump($e->getMessage());
    }
}
