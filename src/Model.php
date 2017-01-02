<?php

namespace Pilulka\Database;
use Pilulka\Core\Facade;

/**
 * Class Model
 * @package Pilulka\Database
 * @method static static first()
 * @method static static[] all()
 * @method static static | static[] where(...$args)
 * @method static static | static[] order($order)
 * @method static static | static[] limit($limit, $offset=null)
 * @method static static | static[] group($group)
 * @method static int count($column=null)
 * @method static int sum($column)
 */
class Model implements \JsonSerializable
{

    protected $table;
    protected $connection;
    protected $touches = [];
    private $modified = [];
    protected $row;

    /** @var ConnectionResolver */
    protected static $resolver;

    public function __get($name)
    {
        return isset($this->modified[$name]) ? $this->modified[$name] : $this->row[$name];
    }

    public function __set($name, $value)
    {
        $this->modified[$name] = $value;
        if (isset($this->row)) {
            $this->row[$name] = $value;
        }
    }


    final protected function getRelated($modelClass, $via = null)
    {
        $this->protectIfRowIsNotLoaded();
        $modelTable = $this->getModelTableByClass($modelClass);
        if (isset($via)) {
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

    final protected function getRelatedMany($modelClass, $filter, $mappedBy = null)
    {
        $this->protectIfRowIsNotLoaded();
        $result = call_user_func_array(
            [$this->row, $this->getModelTableByClass($modelClass)],
            $filter
        );
        if (isset($mappedBy)) {
            $result->via($mappedBy);
        }
        return new ModelCollection(
            $result,
            $modelClass
        );
    }

    /**
     * @param array $data
     * @return static
     */
    public function save(array $data = [])
    {
        $this->fill($data);
        if (isset($this->row)) {
            $this->row->update($this->modified);
        } else {
            $this->row = static::getQuery()->insert(
                $this->getModelTableByClass(static::class),
                $this->modified
            );
        }
        return $this;
    }

    public function fill($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    private function getModelTableByClass($class)
    {
        return (new \ReflectionClass($class))->newInstance()->table();
    }

    private function protectIfRowIsNotLoaded()
    {
        if (!isset($this->row)) {
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

    public function __call($method, $arguments = [])
    {
        $query = $this->getQuery()->table($this->table());
        return call_user_func_array([$query, $method], $arguments);
    }

    public static function __callStatic($method, $arguments = [])
    {
        $instance = new static;
        return call_user_func_array([$instance, $method], $arguments);
    }

    private function resolveConnection()
    {
        if (!isset(self::$resolver)) {
            throw new Exception(
                "Connection resolver is not defined. " .
                "Check your application setup."
            );
        }
        return self::$resolver->instance($this->connection);
    }

    public function delete()
    {
        if (isset($this->row)) {
            $this->row->delete();
            $this->reset();
        }
    }

    private function reset()
    {
        $this->modified = [];
        $this->row = null;
    }

    public static function setConnectionResolver($resolver)
    {
        if (!$resolver instanceof ConnectionResolver && !$resolver instanceof Facade) {
            throw new Exception(
                "Resolver must instance of ConnectionResolver " .
                "or to it's Facade class."
            );
        }
        self::$resolver = $resolver;
    }

    public function jsonSerialize($options = 0)
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
