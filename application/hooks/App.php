<?php

defined('APPPATH') || define('APPPATH', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
/**
* Bootstrap
*/
class App
{
    private static $CI = null;
    /**
     * user object
     * @var null | \Res\Model\User
     */
    private static $user = null;
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
        ini_set("session.cookie_httponly", 1);
        ini_set('session.name','res_sess');
        ini_set('session.use_strict_mode', 1);
        session_start();
        if (!isset($_SESSION['USER'])) {
            return;
        }
        self::$user = new \Res\Model\User();
        self::$user->clone($_SESSION['USER']);
    }

    public static function getUser()
    {
        return self::$user;
    }

    public static function runBeforeMethod()
    {
        self::$CI =& get_instance();
    }

    public static function view(string $name, array $params = [])
    {
        $path = realpath(VIEWPATH . "{$name}.php");
        if (!$path) {
            show_404();
        }
        $params['CI'] = self::$CI;
        self::$CI->load->view($name, $params);
    }

    public static function includeJs(string $name)
    {
        $jsPath = realpath(ROOT_PATH . "assets/dest/{$name}");
        $file = "/assets/{$name}";
        if (!$jsPath) {
            echo '<script type="text/javascript">"' . $name . ' is not exists"</script>';
            return;
        }
        echo '<script type="text/javascript" src="' . $file . '"></script>';
    }
}
