<?php

namespace Pilulka\Database;

class ModelCollection implements \Iterator
{

    private $iterator;
    private $modelClass;

    /**
     * @param \Iterator $iterator
     * @param $modelClass
     */
    public function __construct(\Iterator $iterator, $modelClass)
    {
        $this->iterator = $iterator;
        $this->modelClass = $modelClass;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    public function __call($method, $arguments=[])
    {
        call_user_func_array([$this->iterator, $method], $arguments);
        return $this;
    }


    public function current()
    {
        return $this->getHydratedModel($this->iterator->current());
    }

    private function getHydratedModel($row)
    {
        return Hydrator::hydrate($this->modelClass, $row);
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        return $this->iterator->rewind();
    }


}