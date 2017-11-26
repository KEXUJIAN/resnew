<?php
namespace Res\Model;

class MY_Model
{
    protected static $columns = [];

    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        } else {
            throw new Exception("Undefined Property");
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
            throw new Exception("");
        }
        $this->$key = $val;
        return true;
    }

    public function get($key)
    {
        if (!function_exists('get_instance')) {
            throw new Exception('Not in CI web environment');
        }
        return get_instance()->$key;
    }

    protected function valueCheck(string $key, $val) : bool
    {
        ;
    }
}
