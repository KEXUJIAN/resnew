<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2017/12/29
 * Time: 21:15
 */

namespace Res\Model;

class SimCard extends MY_Model
{
    const TABLE = 'simcards';
    const COLUMNS = ['id', 'userId', 'phoneNumber', 'label', 'carrier', 'place', 'imsi', 'status', 'statusDescription', 'idCard', 'servicePassword', 'timeAdded', 'timeModified', 'deleted',];

    protected $id = null;
    protected $idIsChanged = false;
    protected $userId = null;
    protected $userIdIsChanged = false;
    protected $phoneNumber = null;
    protected $phoneNumberIsChanged = false;
    protected $label = null;
    protected $labelIsChanged = false;
    protected $carrier = null;
    protected $carrierIsChanged = false;
    protected $place = null;
    protected $placeIsChanged = false;
    protected $imsi = null;
    protected $imsiIsChanged = false;
    protected $status = null;
    protected $statusIsChanged = false;
    protected $statusDescription = null;
    protected $statusDescriptionIsChanged = false;
    protected $idCard = null;
    protected $idCardIsChanged = false;
    protected $servicePassword = null;
    protected $servicePasswordIsChanged = false;
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
    const CARRIER_THIRD_PARTY = 3;

    const LABEL_CARRIER = [
        0 => '电信',
        1 => '移动',
        2 => '联通',
        3 => '虚拟运营商',
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

    public function phoneNumber($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->phoneNumber;
        }
        $ret = $this->phoneNumber;
        if ($ret !== $value) {
            $this->phoneNumber = $value;
            $this->phoneNumberIsChanged = true;
        }
        return $this->phoneNumber;
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

    public function place($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->place;
        }
        $ret = $this->place;
        if ($ret !== $value) {
            $this->place = $value;
            $this->placeIsChanged = true;
        }
        return $this->place;
    }

    public function imsi($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->imsi;
        }
        $ret = $this->imsi;
        if ($ret !== $value) {
            $this->imsi = $value;
            $this->imsiIsChanged = true;
        }
        return $this->imsi;
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

    public function idCard($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->idCard;
        }
        $ret = $this->idCard;
        if ($ret !== $value) {
            $this->idCard = $value;
            $this->idCardIsChanged = true;
        }
        return $this->idCard;
    }

    public function servicePassword($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->servicePassword;
        }
        $ret = $this->servicePassword;
        if ($ret !== $value) {
            $this->servicePassword = $value;
            $this->servicePasswordIsChanged = true;
        }
        return $this->servicePassword;
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
