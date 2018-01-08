<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/7
 * Time: 15:22
 */

namespace Res\Util;

use AppService;
use App;

class Permission
{
    public $origin = '';
    public $mapped = '';
    public $permission = [];
    const NO_AUTH = 'NO_AUTH';
    const AUTH = 'AUTH';

    public function __construct($originUri, $mappedUri)
    {
        $this->permission = AppService::getResCfg()->getItem('permission');
        $this->origin = $originUri;
        $this->mapped = $mappedUri;
    }

    public function check()
    {
        $role = App::getUser() ? App::getUser()->role() : self::NO_AUTH;
        // 快速搜索, 是否在 key 中
        if (array_key_exists($this->mapped, $this->permission)) {
            $roles = $this->permission[$this->mapped];
            if (self::NO_AUTH !== $role && in_array(self::AUTH, $roles, true)) {
                return;
            }
            if (in_array($role, $roles, true)) {
                return;
            }
            if (self::NO_AUTH === $role) {
                header('Location: /user/login');
                exit;
            }
            $this->show_403($this->origin);
        }
        $accept = false;
        $matched = false;
        foreach ($this->permission as $rule => $roles) {
            if (!preg_match("#^{$rule}#", $this->mapped)) {
                continue;
            }
            if (self::NO_AUTH !== $role && in_array(self::AUTH, $roles, true)) {
                return;
            }
            $matched = true;
            if (in_array($role, $roles)) {
                $accept = true;
                break;
            }
        }
        // 没有在规则里
        if (!$matched) {
            return;
        }
        // 在规则里并拥有权限
        if ($matched && $accept) {
            return;
        }
        if (self::NO_AUTH === $role) {
            header('Location: /user/login');
            exit;
        }
        $this->show_403($this->origin);
    }

    public function show_403(string $uri = '')
    {
        $message = "请求页面或 API \"{$uri}\" 失败, 原因: 没有相应权限。";
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (false !== strpos($accept, 'application/json')) {
            echo json_encode([
                'result' => false,
                'message' => $message,
            ]);
            exit(403);
        }
        show_error($message, 403, '403 没有权限');
    }
}