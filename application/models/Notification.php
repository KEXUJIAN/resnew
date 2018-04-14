<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 21:14
 */

namespace Res\Model;


class Notification extends MY_Model
{
    const TABLE   = 'notifications';
    const COLUMNS = ['id', 'userId', 'message', 'read', 'status', 'extra', 'timeAdded', 'timeModified', 'deleted',];

    protected $id                    = null;
    protected $idIsChanged           = false;
    protected $userId                = null;
    protected $userIdIsChanged       = false;
    protected $message               = null;
    protected $messageIsChanged      = false;
    protected $read                  = null;
    protected $readIsChanged         = false;
    protected $status                = null;
    protected $statusIsChanged       = false;
    protected $extra                 = null;
    protected $extraIsChanged        = false;
    protected $timeAdded             = null;
    protected $timeAddedIsChanged    = false;
    protected $timeModified          = null;
    protected $timeModifiedIsChanged = false;
    protected $deleted               = null;
    protected $deletedIsChanged      = false;

    const READ_NO    = 0;
    const READ_YES   = 1;
    const LABEL_READ = [
        0 => 'No',
        1 => 'Yes',
    ];

    const STATUS_NEW   = 0;
    const STATUS_DONE  = 1;
    const LABEL_STATUS = [
        0 => 'new',
        1 => 'done',
    ];

    const DELETED_YES   = 1;
    const DELETED_NO    = 0;
    const LABEL_DELETED = [
        0 => 'Not Deleted',
        1 => 'Deleted',
    ];

    public function __construct()
    {
        $now                         = date('Y-m-d H:i:s');
        $this->timeAdded             = $now;
        $this->timeAddedIsChanged    = true;
        $this->timeModified          = $now;
        $this->timeModifiedIsChanged = true;
        $this->read                  = self::READ_NO;
        $this->readIsChanged         = true;
        $this->status                = self::STATUS_NEW;
        $this->statusIsChanged       = true;
        $this->deleted               = self::DELETED_NO;
        $this->deletedIsChanged      = true;
    }

    public function id($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->id;
        }
        $ret = $this->id;
        if ($ret !== $value) {
            $this->id          = $value;
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
            $this->userId          = $value;
            $this->userIdIsChanged = true;
        }
        return $this->userId;
    }

    public function message($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->message;
        }
        $ret = $this->message;
        if ($ret !== $value) {
            $this->message          = $value;
            $this->messageIsChanged = true;
        }
        return $this->message;
    }

    public function read($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->read;
        }
        $ret = $this->read;
        if ($ret !== $value) {
            $this->read          = $value;
            $this->readIsChanged = true;
        }
        return $this->read;
    }

    public function status($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->status;
        }
        $ret = $this->status;
        if ($ret !== $value) {
            $this->status          = $value;
            $this->statusIsChanged = true;
        }
        return $this->status;
    }

    public function extra($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->extra;
        }
        $ret = $this->extra;
        if ($ret !== $value) {
            $this->extra          = $value;
            $this->extraIsChanged = true;
        }
        return $this->extra;
    }

    public function timeAdded($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->timeAdded;
        }
        $ret = $this->timeAdded;
        if ($ret !== $value) {
            $this->timeAdded          = $value;
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
            $this->timeModified          = $value;
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
            $this->deleted          = $value;
            $this->deletedIsChanged = true;
        }
        return $this->deleted;
    }
}