<?php
namespace Res\Model;

use \Exception;
use \AppService;

/**
 * 模型仅用于执行简单的查询
 * 复杂的查询还是要自己写
 */
class MY_Model
{
    const COLUMNS = [];
    // @ 用于表示 like, () 用于表示 in
    const OPERATOR = ['=', '>', '<', '<>', '!=', '>=', '<=', '@', '()'];
    const TABLE = '';
    const VAL_NOT_SET = 'value_not_set';
    protected static $cache = [];

    public function clone(array $parms)
    {
        foreach (static::COLUMNS as $field) {
            if (!isset($parms[$field])) {
                continue;
            }
            $this->$field = $parms[$field];
            $fieldChangeed = "{$field}IsChanged";
            $this->$fieldChangeed = false;
        }
    }

    public function obj2Array(array $exclude = [])
    {
        $arr = [];
        $exclude = array_flip($exclude);
        foreach (static::COLUMNS as $column) {
            if ($exclude && array_key_exists($column, $exclude)) {
                continue;
            }
            $arr[$column] = $this->$column;
        }
        return $arr;
    }

    public function save()
    {
        $table = static::TABLE;
        $fields = static::COLUMNS;
        $columns = [];
        foreach ($fields as $index => $column) {
            $isChanged = "{$column}IsChanged";
            if (!$this->$isChanged) {
                continue;
            }
            $columns[] = $column;
        }

        if (!$columns) {
            return false;
        }
        $pdo = AppService::getPDO();

        if ($this->id === null) {
            $insertFields = strtolower(implode(',', $columns));
            $insertValues = [];
            foreach ($columns as $column) {
                $insertValues[] = ":{$column}";
            }
            $insertValues = implode(',', $insertValues);
            $sql = "INSERT INTO {$table}({$insertFields}) VALUES({$insertValues})";
            $sth = $pdo->prepare($sql);
            foreach ($columns as $column) {
                $sth->bindValue(":{$column}", $this->$column);
            }
            $sth->execute();
            $this->id = $pdo->lastInsertId();
            foreach ($columns as $column) {
                $isChanged = "{$column}IsChanged";
                $this->$isChanged = false;
            }
            return true;
        }
        $updateFields = [];
        foreach ($columns as $index => $column) {
            $updateFields[] = strtolower($column) . " = :{$column}";
        }
        $updateFields = implode(',', $updateFields);
        $sql = "UPDATE {$table} SET {$updateFields} WHERE id = :id";
        $sth = $pdo->prepare($sql);
        foreach ($columns as $column) {
            $sth->bindValue(":{$column}", $this->$column);
        }
        $sth->bindValue(':id', $this->id);
        $sth->execute();
        $influence = $sth->rowCount();
        return $influence ? true : false;
    }

    public static function clearCache(string $table = '', $id = '') : bool
    {
        if (!self::$cache) {
            return false;
        }
        if (!$table) {
            self::$cache = [];
            return true;
        }
        if ($id === '' && isset(self::$cache[$table])) {
            unset(self::$cache[$table]);
            return true;
        } elseif ($id === '') {
            return false;
        }
        if (isset(self::$cache[$table][$id])) {
            unset(self::$cache[$table][$id]);
            return true;
        }
        return false;
    }

    public static function get($id, bool $for_update = false)
    {
        if ($id === null) {
            return null;
        }
        $table = static::TABLE;
        $fields = static::COLUMNS;
        foreach ($fields as $index => $column) {
            $fields[$index] = strtolower($column) . " AS {$column}";
        }
        $fields = implode(',', $fields);
        $sql = "SELECT {$fields} FROM {$table} WHERE id = :id ";
        if ($for_update) {
            $sql .= 'FOR UPDATE';
        }
        $pdo = AppService::getPDO();
        $sth = $pdo->prepare($sql);
        $sth->execute([':id' => $id]);
        $ret = $sth->fetch();
        $sth->closeCursor();
        if (!$ret) {
            return null;
        }
        $o = new static();
        $o->clone($ret);
        return $o;
    }

    public static function getOne(array $conf = [], array $orderBy = [])
    {
        $ret = static::getList($conf, $orderBy, 1);
        if (!$ret) {
            return null;
        }
        return $ret[0];
    }

    public static function getList(array $conf = [], array $orderBy = [], $limit = '', $offset = '') : array
    {
        $result = [];
        $table = static::TABLE;
        $fields = static::COLUMNS;
        foreach ($fields as $index => $column) {
            $fields[$index] = strtolower($column) . " AS {$column}";
        }
        $fields = implode(',', $fields);

        $whereStr = '';
        $whereValues = [];
        if ($conf) {
            $where = self::buildWhere($conf);
            $whereStr = 'WHERE ' . $where['string'];
            $whereValues = $where['array'];
        }
        // var_dump($where);
        $orderByStr = '';
        if ($orderBy) {
            $tmp = [];
            foreach ($orderBy as $key => $value) {
                $tmp[] = strtolower($key) . ' ' . strtoupper($value);
            }
            $tmp = implode(',', $tmp);
            $orderByStr = "ORDER BY {$tmp}";
        }

        $limitStr = '';
        if (is_numeric($limit) && is_numeric($offset)) {
            $limitStr = "LIMIT {$limit} OFFSET {$offset}";
        } elseif (is_numeric($limit)) {
            $limitStr = "LIMIT {$limit}";
        }

        $sql = "SELECT {$fields} FROM {$table} {$whereStr} {$orderByStr} {$limitStr}";
        $pdo = AppService::getPDO();
        $sth = $pdo->prepare($sql);
        $sth->execute($whereValues);
        $ret = $sth->fetchAll();
        // var_dump($sql, $ret);
        foreach ($ret as $row) {
            $o = new static();
            $o->clone($row);
            $result[] = $o;
        }
        return $result;
    }

    public static function getCount(array $conf = []) : int
    {
        $result = 0;
        $table = static::TABLE;
        $whereStr = '';
        $whereValues = [];
        if ($conf) {
            $where = self::buildWhere($conf);
            $whereStr = 'WHERE ' . $where['string'];
            $whereValues = $where['array'];
        }

        $sql = "SELECT count(1) AS _count FROM {$table} {$whereStr}";
        $pdo = AppService::getPDO();
        $sth = $pdo->prepare($sql);
        $sth->execute($whereValues);
        $ret = $sth->fetch();
        $sth->closeCursor();
        if (!$ret) {
            return $result;
        }
        $result = intval($ret['_count']);
        return $result;
    }

    public static function hidden(array $ids)
    {
        if (!in_array('deleted', static::COLUMNS)) {
            return false;
        }
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

    public static function buildWhere(array &$conf) : array
    {
        $validCols = static::COLUMNS;
        $validCols = array_flip($validCols);
        $validOperator = self::OPERATOR;
        $validOperator = array_flip($validOperator);

        $whereStr = [];
        $whereVal = [];
        foreach ($conf as $key => $value) {
            if (!preg_match('#(\w+)(\W+)?#', $key, $match)) {
                throw new Exception("Invalid where statement key format. should be {colName[operator]}");
            }
            $column = $match[1];
            if (!array_key_exists($column, $validCols)) {
                throw new Exception("Invalid column name: {$column}");
            }
            $operator = $match[2] ?? '=';
            if (!array_key_exists($operator, $validOperator)) {
                throw new Exception("Invalid where statement operator: {$operator}");
            }
            if ('()' === $operator) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $whereStr[] = strtolower($column) . " in ({$value})";
                continue;
            }
            $whereVal[":{$column}"] = $value;
            if ('@' === $operator) {
                $operator = 'LIKE';
                $whereVal[":{$column}"] = "%{$value}%";
            }
            $whereStr[] = strtolower($column) . " {$operator} :{$column}";
        }
        $whereStr = implode(' AND ', $whereStr);
        $where = [
            'string' => $whereStr,
            'array' => $whereVal,
        ];
        return $where;
    }
}
