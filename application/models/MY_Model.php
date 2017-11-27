<?php
namespace Res\Model;

use \Exception;
use \AppService;

class MY_Model
{
    const COLUMNS = [];
    const TABLE = '';
    const STATUS_LIST = [];

    public static function get($id, bool $for_update = false) : array
    {
        $table = static::TABLE;
        $sql = "SELECT * FROM {$table} WHERE id = :id ";
        if ($for_update) {
            $sql .= 'FOR UPDATE';
        }
        $pdo = AppService::getPDO();
        $sth = $pdo->prepare($sql);
        $sth->execute([':id' => $id]);
        $ret = $sth->fetch();
        $sth->closeCursor();
        if (!$ret) {
            return [];
        }
        return $ret;
    }

    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        } else {
            throw new Exception('Undefined Property: ' . self::class . "->{$key}");
        }
    }

    public function getCI($key)
    {
        if (!function_exists('get_instance')) {
            throw new Exception('Not in CI web environment');
        }
        return get_instance()->$key;
    }
}
