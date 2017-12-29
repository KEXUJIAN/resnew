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
        if (isset(self::$instances[\Res\Util\MyPDO::class])) {
            return self::$instances[\Res\Util\MyPDO::class];
        }
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

    public static function generateModel($model)
    {
        $fields = $model::COLUMNS;
        $objectFields = [];
        $functions = [];
        $database = [];
        $tab = str_repeat(' ', 4);
        foreach ($fields as $column) {
            $database[] = strtolower($column);
            $objectFields[] = "protected \${$column} = null;";
            $objectFields[] = "protected \${$column}IsChanged = false;";
            $function = [];
            $function[] = "public function {$column}" . '($value = MY_Model::VAL_NOT_SET)';
            $function[] = "{";
            $function[] = $tab . 'if ($value === MY_Model::VAL_NOT_SET) {';
            $function[] = str_repeat($tab, 2) . 'return $this->' . $column . ';';
            $function[] = $tab . '}';
            $function[] = $tab . '$ret = $this->' . $column . ';';
            $function[] = $tab . 'if ($ret !== $value) {';
            $function[] = str_repeat($tab, 2) . '$this->' . $column . ' = $value;';
            $function[] = str_repeat($tab, 2) . '$this->' . $column . 'IsChanged = true;';
            $function[] = $tab . '}';
            $function[] = $tab . 'return $this->' . $column . ';';
            $function[] = "}";
            $functions[] = implode("\n", $function);
        }
        echo implode("\n", $objectFields), "\n\n";
        echo implode("\n\n", $functions), "\n\n";
        echo implode("\n", $database);
    }
}
