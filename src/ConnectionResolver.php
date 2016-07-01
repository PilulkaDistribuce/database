<?php

namespace Pilulka\Database;

use Pilulka\Core\ServiceManager;

class ConnectionResolver extends ServiceManager
{

    public function addService($name, $service)
    {
        if (! $service instanceof Connection) {
            throw new \RuntimeException(
                sprintf(
                    "Instance have to be instance of %s",
                    Connection::class
                )
            );
        }
        parent::addService($name, $service);
    }

}