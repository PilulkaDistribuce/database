<?php

namespace Pilulka\Database;

abstract class Model implements \JsonSerializable
{

    protected $table;
    protected $connection;
    protected $touches = [];
    protected $row;

    /** @var ConnectionResolver */
    protected static $resolver;

    public function __get($name)
    {
        return $this->row[$name];
    }

    final protected function getRelated($modelClass, $via=null)
    {
        $this->protectIfRowIsNotLoaded();
        $modelTable = $this->getModelTableByClass($modelClass);
        if(isset($via)) {
            StructureConvention::addReference(
                $this->table(),
                $via,
                $modelTable
            );
        }
        $accessor = $via ?: $modelTable;
        return $this->row->$accessor
            ? Hydrator::hydrate($modelClass, $this->row->$accessor)
            : null;
    }

    protected function getRelatedMany($modelClass, $params, $mappedBy=null)
    {
        $this->protectIfRowIsNotLoaded();
        $result = call_user_func_array(
            [$this->row, $this->getModelTableByClass($modelClass)],
            $params
        );
        if(isset($mappedBy)) {
            $result->via($mappedBy);
        }
        return new ModelCollection(
            $result,
            $modelClass
        );
    }

    public function save(array $data)
    {
        if(isset($this->row)) {

        } else {
            $this->row = static::getQuery()->insert(
                $this->getModelTableByClass(static::class),
                $data
            );
        }
    }

    private function getModelTableByClass($class)
    {
        return (new \ReflectionClass($class))->newInstance()->table();
    }

    private function protectIfRowIsNotLoaded()
    {
        if(!isset($this->row)) {
            throw new Exception(
                sprintf("Row data is not loaded for: %s.", static::class)
            );
        }
    }

    private function getQuery()
    {
        return new QueryBuilder(
            $this->resolveConnection(),
            static::class
        );
    }

    public function __call($method, $arguments=[])
    {
        $query = $this->getQuery()->table($this->table());
        return call_user_func_array([$query, $method], $arguments);
    }

    public static function __callStatic($method, $arguments=[])
    {
        $instance = new static;
        return call_user_func_array([$instance, $method], $arguments);
    }

    private function resolveConnection()
    {
        if(!isset(self::$resolver)) {
            throw new Exception(
                "Connection resolver is not defined. " .
                "Check your application setup."
            );
        }
        return self::$resolver->instance($this->connection);
    }

    public static function setConnectionResolver(ConnectionResolver $resolver)
    {
        self::$resolver = $resolver;
    }

    public function jsonSerialize($options=0)
    {
        return json_encode($this->__toArray(), $options);
    }

    public function __toArray()
    {
        return iterator_to_array($this->row);
    }

    final private function table()
    {
        return $this->table;
    }

}