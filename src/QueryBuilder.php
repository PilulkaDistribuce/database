<?php

namespace Pilulka\Database;

class QueryBuilder
{

    /** @var  Connection */
    private $orm;
    private $query;
    private $modelClass;

    public function __construct(Connection $orm, $modelClass)
    {
        $this->orm = $orm;
        $this->modelClass = $modelClass;
    }

    public function __call($method, $arguments=[])
    {
        if(!isset($this->query)) {
            $this->query =  $this->orm;
        }
        $this->query = call_user_func_array([$this->query, $method], $arguments);
        return $this;
    }

    public function insert($table, $data)
    {
        $this->table($table);
        return $this->getQuery()->insert($data);
    }

    public function table($table)
    {
        return $this->__call($table);
    }

    public function all()
    {
        return $this->getCollection();
    }

    public function first()
    {
        foreach ($this->limit(1)->all() as $model) {
            return $model;
        }
    }

    private function getCollection()
    {
        return new ModelCollection(
            $this->query,
            $this->modelClass
        );
    }

    private function getQuery()
    {
        return $this->query;
    }

    public function __toString()
    {
        return (string)$this->getQuery();
    }

}