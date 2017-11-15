<?php
namespace Res\Config;

/**
* Base config
*/
class BaseConfig
{

    public function getAll()
    {
        return get_object_vars($this);
    }

    public function getItem(string $name, string $sub_item = null)
    {
        if ($sub_item) {
            return $this->$name[$sub_item] ?? null;
        }
        return $this->$name ?? null;
    }
}
