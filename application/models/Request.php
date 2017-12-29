<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/12/29
 * Time: 21:59
 */

namespace Res\Model;


class Request extends MY_Model
{
    const TABLE = 'requests';
    const COLUMNS = ['id', 'fromUserId', 'toUserId', 'assetId', 'assetType','type', 'status', 'timeAdded', 'timeModified', 'deleted',];

    protected $id = null;
    protected $idIsChanged = false;
    protected $fromUserId = null;
    protected $fromUserIdIsChanged = false;
    protected $toUserId = null;
    protected $toUserIdIsChanged = false;
    protected $assetId = null;
    protected $assetIdIsChanged = false;
    protected $assetType = null;
    protected $assetTypeIsChanged = false;
    protected $type = null;
    protected $typeIsChanged = false;
    protected $status = null;
    protected $statusIsChanged = false;
    protected $timeAdded = null;
    protected $timeAddedIsChanged = false;
    protected $timeModified = null;
    protected $timeModifiedIsChanged = false;
    protected $deleted = null;
    protected $deletedIsChanged = false;

    const ASSET_TYPE_PHONE = 0;
    const ASSET_TYPE_SIM_CARD = 1;
    const LABEL_ASSET_TYPE = [
        0 => 'phone',
        1 => 'simcard',
    ];

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

    public function fromUserId($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->fromUserId;
        }
        $ret = $this->fromUserId;
        if ($ret !== $value) {
            $this->fromUserId = $value;
            $this->fromUserIdIsChanged = true;
        }
        return $this->fromUserId;
    }

    public function toUserId($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->toUserId;
        }
        $ret = $this->toUserId;
        if ($ret !== $value) {
            $this->toUserId = $value;
            $this->toUserIdIsChanged = true;
        }
        return $this->toUserId;
    }

    public function assetId($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->assetId;
        }
        $ret = $this->assetId;
        if ($ret !== $value) {
            $this->assetId = $value;
            $this->assetIdIsChanged = true;
        }
        return $this->assetId;
    }

    public function assetType($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->assetType;
        }
        $ret = $this->assetType;
        if ($ret !== $value) {
            $this->assetType = $value;
            $this->assetTypeIsChanged = true;
        }
        return $this->assetType;
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