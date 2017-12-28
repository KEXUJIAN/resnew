<?php
namespace Res\Model;

/**
* User
*/
class User extends MY_Model
{
    const TABLE = 'users';
    const COLUMNS = ['id', 'name', 'username', 'password', 'passwordSalt', 'email', 'role', 'timeAdded', 'timeModified', 'deleted',];

    protected $id = null;
    protected $idIsChanged = false;
    protected $name = null;
    protected $nameIsChanged = false;
    protected $username = null;
    protected $usernameIsChanged = false;
    protected $password = null;
    protected $passwordIsChanged = false;
    protected $passwordSalt = null;
    protected $passwordSaltIsChanged = false;
    protected $email = null;
    protected $emailIsChanged = false;
    protected $role = null;
    protected $roleIsChanged = false;
    protected $timeAdded = null;
    protected $timeAddedIsChanged = false;
    protected $timeModified = null;
    protected $timeModifiedIsChanged = false;
    protected $deleted = null;
    protected $deletedIsChanged = false;

    const ROLE_MANAGER = 0;
    const ROLE_EMPLOYEE = 1;
    const LABEL_ROLE = [
        0 => 'Manager',
        1 => 'Employee',
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
        $this->role = self::ROLE_EMPLOYEE;
        $this->roleIsChanged = true;
        $this->deleted = self::DELETED_NO;
        $this->deletedIsChanged = true;
    }

    public function name($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->name;
        }
        $ret = $this->name;
        if ($ret !== $value) {
            $this->name = $value;
            $this->nameIsChanged = true;
        }
        return $this->name;
    }

    public function username($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->username;
        }
        $ret = $this->username;
        if ($ret !== $value) {
            $this->username = $value;
            $this->usernameIsChanged = true;
        }
        return $this->username;
    }

    public function password($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->password;
        }
        $ret = $this->password;
        if ($ret !== $value) {
            $this->password = $value;
            $this->passwordIsChanged = true;
        }
        return $this->password;
    }

    public function passwordSalt($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->passwordSalt;
        }
        $ret = $this->passwordSalt;
        if ($ret !== $value) {
            $this->passwordSalt = $value;
            $this->passwordSaltIsChanged = true;
        }
        return $this->passwordSalt;
    }

    public function email($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->email;
        }
        $ret = $this->email;
        if ($ret !== $value) {
            $this->email = $value;
            $this->emailIsChanged = true;
        }
        return $this->email;
    }

    public function role($value = MY_Model::VAL_NOT_SET)
    {
        if ($value === MY_Model::VAL_NOT_SET) {
            return $this->role;
        }
        $ret = $this->role;
        if ($ret !== $value) {
            $this->role = $value;
            $this->roleIsChanged = true;
        }
        return $this->role;
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
