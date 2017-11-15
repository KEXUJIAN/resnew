<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_system'] = function () {
    require_once realpath(APPPATH . 'hooks/Autoloader/Autoloader.php');
    require_once realpath(APPPATH . 'config/App/Autoload.php');
    require_once realpath(APPPATH . 'hooks/Service.php');
    class_alias('\Res\Hook\Service', 'Res\Service');

    // initialize our psr-4 style autoloader
    $loader = Res\Service::getLoader();
    $loader->initialize(new \Res\Config\Autoload());
    $loader->register();
    // database
    Res\Service::getPDO();
};

$hook['pre_controller'] = function () {
    // laod our app specific bootstrap module
};
