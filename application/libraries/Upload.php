<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2017/12/29
 * Time: 23:03
 */

namespace Res\Util;


class Upload
{
    private $uploadPath = ROOT_PATH . 'Upload';

    public function __construct()
    {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777);
        }
    }

    public function resolve(array &$files)
    {
        $fileLen = count($files['name']);
        $resolved = [];
        for ($i = 0; $i < $fileLen; ++$i) {
            foreach ($files as $key => $arr) {
                $resolved[$i][$key] = $arr[$i];
            }
        }
        return $resolved;
    }
}