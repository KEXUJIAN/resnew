<?php
namespace Res\Model;

use \Exception;
use \AppService;

class MY_Model
{
    const COLUMNS = [];
    const TABLE = '';
    const STATUS_LIST = [];
    const DELETED_YES = 1;
    const DELETED_NO  = 0;

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

    public static function hidden(array $ids)
    {
        $pdo = AppService::getPDO();
        $table = static::TABLE;
        $deleted_yes = static::DELETED_YES;
        $deleted_no  = static::DELETED_NO;
        $sth = $pdo->prepare("UPDATE {$table} SET deleted = {$deleted_yes} WHERE id = :id AND deleted = {$deleted_no}");
        $row_count = 0;
        foreach ($ids as $id) {
            if (!is_scalar($id)) {
                throw new Exception("Invalid id");
            }
            $sth->execute([':id' => $id]);
            $row_count += $sth->rowCount();
        }
        return $row_count;
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
