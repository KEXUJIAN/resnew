<?php
namespace Res\Config;

/**
* Base config
*/
class BaseConfig
{

    public function getCfg()
    {
        return get_object_vars($this);
    }
}
