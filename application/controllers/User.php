<?php

use \Res\Model\User as UserModel;
use Res\Model\Notification;

/**
* User controller
*/
class User extends CI_Controller
{
    public function profile(string $name = 'user', $id = null)
    {
        $pdo = AppService::getPDO();
        $table = Notification::TABLE;
        $sql = "UPDATE {$table} SET `read` = :readYes WHERE userid = :userId AND status = :readNo";
        $sth = $pdo->prepare($sql);
        $sth->execute([
            ':readYes' => Notification::READ_YES,
            ':userId' => App::getUser()->id(),
            ':readNo' => Notification::READ_NO,
        ]);
        $valid = [
            'user',
            'notification',
            'request',
        ];
        $panel = 'user';
        if ($name && in_array($name, $valid, true)) {
            $panel = $name;
        }
        App::view('user/profile', [
            'user' => App::getUser(),
            'panel' => $panel,
            'id' => $id,
            'titleNavClass' => 'container-fluid',
        ]);
    }

    public function info($id)
    {
        $user = UserModel::get($id);
        if (!$user || $user->deleted() === UserModel::DELETED_YES) {
            show_error('您查看的用户不存在', 500, '找不到用户');
        }
        App::view('user/user-info', ['user' => $user]);
    }

    public function reset()
    {
        $password = $_POST['password'] ?? '';
        $passwordCfm = $_POST['passwordCfm'] ?? '';
        $response = [
            'result' => true,
            'message' => '密码修改成功',
        ];
        if (!$password || !$passwordCfm) {
            $response['result'] = false;
            $response['message'] = '新密码 / 确认密码不能为空';
            echo json_encode($response);
            return;
        }
        if ($password !== $passwordCfm) {
            $response['result'] = false;
            $response['message'] = '两次密码不符';
            echo json_encode($response);
            return;
        }
        $user = App::getUser();
        $password = sha1($password . $user->passwordSalt());
        $user->password($password);
        $user->save();
        $_SESSION['USER'] = $user->obj2Array();
        echo json_encode($response);
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
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /user/login');
    }

    public function select2()
    {
        $c = [];
        $list = UserModel::getList($c);
        $response = [];
        echo json_encode($response);
    }
}
