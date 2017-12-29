<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2017/12/29
 * Time: 23:28
 */

namespace Res\Model;


class UploadFile
{
    const TABLE = 'uploadfiles';
    const COLUMNS = ['id', 'uploadByUser', 'type', 'originName', 'fileName', 'data', 'status', 'timeAdded', 'timeModified', 'deleted',];

    protected $id = null;
    protected $idIsChanged = false;
    protected $uploadByUser = null;
    protected $uploadByUserIsChanged = false;
    protected $type = null;
    protected $typeIsChanged = false;
    protected $originName = null;
    protected $originNameIsChanged = false;
    protected $fileName = null;
    protected $fileNameIsChanged = false;
    protected $data = null;
    protected $dataIsChanged = false;
    protected $status = null;
    protected $statusIsChanged = false;
    protected $timeAdded = null;
    protected $timeAddedIsChanged = false;
    protected $timeModified = null;
    protected $timeModifiedIsChanged = false;
    protected $deleted = null;
    protected $deletedIsChanged = false;

    const DELETED_YES = 1;
    const DELETED_NO = 0;
    const LABEL_DELETED = [
        0 => 'Not Deleted',
        1 => 'Deleted',
    ];

    public function __construct()
    {
        $now = date('Y-m-d H:i:s');
        $this->timeAdded = $now;
        $this->timeAddedIsChanged = true;
        $this->timeModified = $now;
        $this->timeModifiedIsChanged = true;
        $this->deleted = self::DELETED_NO;
        $this->deletedIsChanged = true;
    }

    public function id($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->id;
        }
        $ret = $this->id;
        if ($ret !== $value) {
            $this->id = $value;
            $this->idIsChanged = true;
        }
        return $this->id;
    }

    public function uploadByUser($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->uploadByUser;
        }
        $ret = $this->uploadByUser;
        if ($ret !== $value) {
            $this->uploadByUser = $value;
            $this->uploadByUserIsChanged = true;
        }
        return $this->uploadByUser;
    }

    public function type($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->type;
        }
        $ret = $this->type;
        if ($ret !== $value) {
            $this->type = $value;
            $this->typeIsChanged = true;
        }
        return $this->type;
    }

    public function originName($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->originName;
        }
        $ret = $this->originName;
        if ($ret !== $value) {
            $this->originName = $value;
            $this->originNameIsChanged = true;
        }
        return $this->originName;
    }

    public function fileName($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->fileName;
        }
        $ret = $this->fileName;
        if ($ret !== $value) {
            $this->fileName = $value;
            $this->fileNameIsChanged = true;
        }
        return $this->fileName;
    }

    public function data($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->data;
        }
        $ret = $this->data;
        if ($ret !== $value) {
            $this->data = $value;
            $this->dataIsChanged = true;
        }
        return $this->data;
    }

    public function status($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->status;
        }
        $ret = $this->status;
        if ($ret !== $value) {
            $this->status = $value;
            $this->statusIsChanged = true;
        }
        return $this->status;
    }

    public function timeAdded($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->timeAdded;
        }
        $ret = $this->timeAdded;
        if ($ret !== $value) {
            $this->timeAdded = $value;
            $this->timeAddedIsChanged = true;
        }
        return $this->timeAdded;
    }

    public function timeModified($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->timeModified;
        }
        $ret = $this->timeModified;
        if ($ret !== $value) {
            $this->timeModified = $value;
            $this->timeModifiedIsChanged = true;
        }
        return $this->timeModified;
    }

    public function deleted($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->deleted;
        }
        $ret = $this->deleted;
        if ($ret !== $value) {
            $this->deleted = $value;
            $this->deletedIsChanged = true;
        }
        return $this->deleted;
    }
}