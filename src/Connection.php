<?php

namespace Pilulka\Database;

class Connection
{
    private $connection;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->connection = new \NotORM(
            $pdo,
            new StructureConvention()
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getRawConnection()
    {
        return $this->connection;
    }

    public function __call($method, $arguments=[])
    {
        return call_user_func_array([$this->connection, $method], $arguments);
    }


}
