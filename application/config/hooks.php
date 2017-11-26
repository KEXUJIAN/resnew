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
    require_once realpath(APPPATH . 'hooks/App.php');
    App::boot();
};

$hook['pre_controller'] = function () {
    // laod our app specific bootstrap module
};
