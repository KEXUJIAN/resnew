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
    const COLUMNS = ['id', 'userId', 'phoneNumber', 'label', 'carrier', 'place', 'imsi', 'status', 'statusDescription', 'timeAdded', 'timeModified', 'deleted',];

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
    protected $timeAdded = null;
    protected $timeAddedIsChanged = false;
    protected $timeModified = null;
    protected $timeModifiedIsChanged = false;
    protected $deleted = null;
    protected $deletedIsChanged = false;

    const STATUS_IN_INVENTORY = 0;
    const STATUS_REQUESTING = 1;
    const STATUS_RENT_OUT = 2;
    const STATUS_BROKEN = 3;
    const STATUS_OTHER = 4;
    const LABEL_STATUS = [
        0 => 'Available',
        1 => 'Applying',
        2 => 'Renting',
        3 => 'Broken',
        4 => 'Other',
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
