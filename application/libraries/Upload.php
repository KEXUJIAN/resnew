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
    const ERR_CODE_UPLOAD_FAIL = 1;
    const ERR_CODE_UNEXPECTED_EXTENSION = 2;
    const ERR_MESSAGE = [
        1 => '文件上传失败',
        2 => '不支持的文件后缀',
    ];
    private $uploadPath = ROOT_PATH . 'Upload';
    private $acceptExt = ['xls', 'xlsx'];

    public function __construct()
    {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777);
        }
    }

    public function check(array &$file) : array
    {
        $error = [];
        if ($file['error']) {
            $error['message'] = self::ERR_MESSAGE[self::ERR_CODE_UPLOAD_FAIL];
            $error['code'] = self::ERR_CODE_UPLOAD_FAIL;
            return $error;
        }
        $ext = pathinfo($file['name'])['extension'];
        if (!in_array($ext, $this->acceptExt)) {
            $error['message'] = self::ERR_MESSAGE[self::ERR_CODE_UNEXPECTED_EXTENSION];
            $error['code'] = self::ERR_CODE_UNEXPECTED_EXTENSION;
            return $error;
        }
        return $error;
    }

    public function saveFile(array &$file)
    {

    }

    public static function resolve(array &$files)
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