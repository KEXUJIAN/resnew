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
    // get request uri
    $uri =& load_class('URI', 'core');
    $originUri = $uri->uri_string ?: '/';
    $mappedUri = implode('/', $uri->rsegments);

    // commit session
    $session_not_immediately_commit_array = ['user/logout', 'user/doLogin'];
    if (!in_array($mappedUri, $session_not_immediately_commit_array)) {
        session_commit();
    }
    // check permission
    $roleLimit = new \Res\Util\Permission($originUri, $mappedUri);
    $roleLimit->check();
};

$hook['post_controller_constructor'] = function () {
    App::runBeforeMethod();
    App::getCI()->load->helper('url');
};
