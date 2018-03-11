<?php
namespace Res\Model;

/**
* Phone
*/
class Phone extends MY_Model
{
    const TABLE = 'phones';
    const COLUMNS = ['id', 'userId', 'type', 'os', 'resolution', 'ram', 'carrier', 'screenSize', 'label', 'imei', 'status', 'statusDescription', 'remark', 'timeAdded', 'timeModified', 'deleted',];

    protected $id = null;
    protected $idIsChanged = false;
    protected $userId = null;
    protected $userIdIsChanged = false;
    protected $type = null;
    protected $typeIsChanged = false;
    protected $os = null;
    protected $osIsChanged = false;
    protected $resolution = null;
    protected $resolutionIsChanged = false;
    protected $ram = null;
    protected $ramIsChanged = false;
    protected $carrier = null;
    protected $carrierIsChanged = false;
    protected $screenSize = null;
    protected $screenSizeIsChanged = false;
    protected $label = null;
    protected $labelIsChanged = false;
    protected $imei = null;
    protected $imeiIsChanged = false;
    protected $status = null;
    protected $statusIsChanged = false;
    protected $statusDescription = null;
    protected $statusDescriptionIsChanged = false;
    protected $remark = null;
    protected $remarkIsChanged = false;
    protected $timeAdded = null;
    protected $timeAddedIsChanged = false;
    protected $timeModified = null;
    protected $timeModifiedIsChanged = false;
    protected $deleted = null;
    protected $deletedIsChanged = false;

    const STATUS_IN_INVENTORY = 0;
    const STATUS_RENT_OUT = 1;
    const STATUS_BROKEN = 2;
    const STATUS_OTHER = 3;
    const STATUS_RENTING = 4;
    const STATUS_RETURNING = 5;
    const LABEL_STATUS = [
        0 => '可借出',
        1 => '已借出',
        2 => '不可用',
        3 => '其他',
        4 => '申请借用',
        5 => '申请归还',
    ];

    const CARRIER_CHINA_TELECOM = 0;
    const CARRIER_CHINA_MOBILE = 1;
    const CARRIER_CHINA_UNICOM = 2;

    const LABEL_CARRIER = [
        0 => '电信',
        1 => '移动',
        2 => '联通',
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

    public function userId($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->userId;
        }
        $ret = $this->userId;
        if ($ret !== $value) {
            $this->userId = $value;
            $this->userIdIsChanged = true;
        }
        return $this->userId;
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

    public function os($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->os;
        }
        $ret = $this->os;
        if ($ret !== $value) {
            $this->os = $value;
            $this->osIsChanged = true;
        }
        return $this->os;
    }

    public function resolution($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->resolution;
        }
        $ret = $this->resolution;
        if ($ret !== $value) {
            $this->resolution = $value;
            $this->resolutionIsChanged = true;
        }
        return $this->resolution;
    }

    public function ram($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->ram;
        }
        $ret = $this->ram;
        if ($ret !== $value) {
            $this->ram = $value;
            $this->ramIsChanged = true;
        }
        return $this->ram;
    }

    public function carrier($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->carrier;
        }
        $ret = $this->carrier;
        if ($ret !== $value) {
            $this->carrier = $value;
            $this->carrierIsChanged = true;
        }
        return $this->carrier;
    }

    public function screenSize($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->screenSize;
        }
        $ret = $this->screenSize;
        if ($ret !== $value) {
            $this->screenSize = $value;
            $this->screenSizeIsChanged = true;
        }
        return $this->screenSize;
    }

    public function label($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->label;
        }
        $ret = $this->label;
        if ($ret !== $value) {
            $this->label = $value;
            $this->labelIsChanged = true;
        }
        return $this->label;
    }

    public function imei($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->imei;
        }
        $ret = $this->imei;
        if ($ret !== $value) {
            $this->imei = $value;
            $this->imeiIsChanged = true;
        }
        return $this->imei;
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

    public function statusDescription($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->statusDescription;
        }
        $ret = $this->statusDescription;
        if ($ret !== $value) {
            $this->statusDescription = $value;
            $this->statusDescriptionIsChanged = true;
        }
        return $this->statusDescription;
    }

    public function remark($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->remark;
        }
        $ret = $this->remark;
        if ($ret !== $value) {
            $this->remark = $value;
            $this->remarkIsChanged = true;
        }
        return $this->remark;
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
