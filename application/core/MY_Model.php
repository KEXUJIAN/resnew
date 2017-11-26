<?php

class MY_Model extends CI_Model
{
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
        if (true) {
            //TODO some check
            $this->$key = $val;
        } else {
            $this->$key = $val;
        }
    }

    public function get($key)
    {
        return get_instance()->$key;
    }

    protected function valueCheck(string $key, $val)
    {

    }
}
