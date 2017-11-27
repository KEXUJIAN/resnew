<?php
namespace Res\Util;

use \PDO;

/**
* PDO wrapper
*/
class MyPDO
{
    private $pdo;

    public function connect(string $dsn, string $user, string $pass)
    {
        $this->pdo = new PDO($dsn, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function prepare(string $sql)
    {
        return $this->pdo->prepare($sql);
    }

    public function exec(string $sql)
    {
        return $this->pdo->exec($sql);
    }

    public function query(string $sql)
    {
        return $this->pdo->query($sql);
    }
}
