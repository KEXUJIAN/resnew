<?php
namespace Res\Hook;

/**
* Service class
*/
class Service
{
    private static $instances = [];

    public static function getLoader()
    {
        return self::singleton(\Res\Hook\Autoloader\Autoloader::class);
    }

    public static function getResCfg()
    {
        $app_config = '\Res\Config\\' . ucfirst(ENVIRONMENT). '\Config';
        return self::singleton($app_config);
    }

    public static function getPDO()
    {
        $config = self::getResCfg()->getItem('db');
        $pdo = self::singleton(\Res\Util\MyPDO::class);
        $db = $config['db'] ?? '';
        if ($db) {
            $db = "dbname={$db}";
        }
        $dsn = "{$config['dsn']}{$db}";
        $user = $config['user'] ?? '';
        $pass = $config['pass'] ?? '';
        $pdo->connect($dsn, $user, $pass);
        return $pdo;
    }

    public static function singleton($class)
    {
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }
        self::$instances[$class] = new $class();
        return self::$instances[$class];
    }
}
