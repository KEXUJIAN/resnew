<?php

/**
* User controller
*/
class User extends CI_Controller
{
    public function login()
    {
        App::view('login');
    }

    public function doLogin()
    {
        sleep(3);
        echo json_encode(['result' => true]);
    }
}
