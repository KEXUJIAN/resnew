<?php
namespace Res\Config;

/**
* Autoload config
*/
class Autoload
{
    /**
     * psr-4 mapping array
     * @var array
     */
    public $psr4 = [];

    public function __construct()
    {
        $this->psr4 = [
            'Res\Config' => [realpath(APPPATH . 'config/App')],
            'Res\Hook'   => [realpath(APPPATH . 'hooks')],
            'Res\Model'  => [realpath(APPPATH . 'models')],
            'Res\Util'   => [realpath(APPPATH . 'libraries')],
            'Res'        => [APPPATH],
        ];
    }
}

