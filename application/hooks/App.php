<?php

defined('APPPATH') || define('APPPATH', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
/**
* Bootstrap
*/
class App
{
    public static $user = null;
    public static function boot()
    {
        require_once realpath(APPPATH . 'hooks/Autoloader/Autoloader.php');
        require_once realpath(APPPATH . 'config/App/Autoload.php');
        require_once realpath(APPPATH . 'hooks/Service.php');
        class_alias('\Res\Hook\Service', '\AppService');

        // initialize our psr-4 style autoloader
        $loader = AppService::getLoader();
        $loader->initialize(new \Res\Config\Autoload());
        $loader->register();
        // database
        AppService::getPDO();
    }
}
