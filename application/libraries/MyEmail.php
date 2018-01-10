<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/6
 * Time: 19:37
 */

namespace Res\Util;

use App;
use AppService;

class MyEmail
{
    private $email = null;
    private $config = null;

    public function __construct()
    {
        $CI = App::getCI();
        $CI->load->library('email');
        $this->email = $CI->email;
        $this->config = AppService::getResCfg()->getItem('mail');
        $this->email->initialize($this->config)
            ->set_mailtype('html');
    }

    public function send(string $subject, string $content, string $to = '') : bool
    {
        $to = $to ?: $this->config['admin'];
        $result = $this->email->to($to)
            ->from($this->config['from'], $this->config['from_name'])
            ->subject($subject)
            ->message(nl2br($content))
            ->send();
//        var_dump($this->email->print_debugger());
        return $result;
    }
}