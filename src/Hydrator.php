<?php

namespace Pilulka\Database;

class Hydrator
{

    public static function hydrate($class, $row)
    {
        $model = (new \ReflectionClass($class))->newInstance();
        $property = new \ReflectionProperty($class, 'row');
        $property->setAccessible(true);
        $property->setValue($model, $row);
        return $model;
    }

}