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
    }
}
