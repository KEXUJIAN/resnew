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

    public static function singleton($class)
    {
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }
        self::$instances[$class] = new $class();
        return self::$instances[$class];
    }
}
