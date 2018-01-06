<?php

use \Res\Model\User as UserModel;

/**
* User controller
*/
class User extends CI_Controller
{
    public function profile()
    {
        App::view('profile');
    }

    public function notification()
    {
        $result = [
            'result' => true,
            'message' => 1,
        ];
        echo json_encode($result);
    }

    public function login()
    {
        App::view('login');
    }

    public function doLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $response = [
            'result' => true,
            'message' => '/assets/phone',
        ];

        if (!$username || !$password) {
            $response['result'] = false;
            $response['message'] = '用户名 / 密码不能为空';
            echo json_encode($response);
            return;
        }

        $user = UserModel::getOne(['username' => $username,]);
        if (!$user) {
            $response['result'] = false;
            $response['message'] = '用户不存在';
            echo json_encode($response);
            return;
        }

        $passwordHash = sha1($password . $user->passwordSalt());
        if ($passwordHash !== $user->password()) {
            $response['result'] = false;
            $response['message'] = '用户名 / 密码错误';
            echo json_encode($response);
            return;
        }
        if (UserModel::ROLE_MANAGER === $user->role()) {
            $response['message'] = '/admin/console';
        }
        $_SESSION['USER'] = $user->obj2Array();
        session_commit();
        echo json_encode($response);
    }

    public function logout()
    {
        $_SESSION = [];
        header('Location: /user/login');
    }
}
