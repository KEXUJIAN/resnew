<?php
namespace Res\Model;

/**
* User
*/
class User extends MY_Model
{
    const TABLE = 'users';
    const COLUMNS = ['id', 'name', 'username', 'password', 'passwordSalt', 'deleted',];

    private $id = null;
    private $idIsChanged = false;
    private $name = null;
    private $nameIsChanged = false;
    private $username = null;
    private $usernameIsChanged = false;
    private $password = null;
    private $passwordIsChnaged = false;
    private $passwordSalt = null;
    private $passwordSaltIsChanged = false;
    private $deleted = null;
    private $deletedIsChanged = false;


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
