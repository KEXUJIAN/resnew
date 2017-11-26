<?php
namespace Res\Model;

use \Exception;

class MY_Model
{
    protected $columns = [];

    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        } else {
            throw new Exception('Undefined Property: ' . self::class . "->{$key}");
        }
    }

    public function __set(string $key, $val)
    {
        if ('columns' === $key) {
            return false;
        }

        if (!array_key_exists($key, $this->columns)) {
            $this->$key = $val;
            return true;
        }
        //TODO some check
        if (!$this->valueCheck($key, $val)) {
            throw new Exception("The type of given value is not suitable");
        }
        $this->$key = $val;
        return true;
    }

    public function getCI($key)
    {
        if (!function_exists('get_instance')) {
            throw new Exception('Not in CI web environment');
        }
        return get_instance()->$key;
    }

    protected function valueCheck(string $key, $val) : bool
    {
        $define = $this->columns[$key] ?? '';
        if ('' === $define) {
            return true;
        }
        $method = "is_{$define}";
        return $method($val);
    }
}
