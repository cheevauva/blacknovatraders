<?php

//declare(strict_types=1);

namespace UUA\Container;

use UUA\Container\Exception\ContainerNotFoundException;
use UUA\FactoryInterface;
use UUA\Factory;

class Container implements \Psr\Container\ContainerInterface
{

    /**
     * @var array
     */
    protected $assetsResolved = [];

    public function __construct(\Closure $assets)
    {
        $this->assetsResolved[spl_object_hash($this)] = $assets($this);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerNotFoundException
     */
    public function get($id)
    {
        if (!isset($this->assetsResolved[$id])) {
            if (class_exists($id, true) || interface_exists($id, true)) {
                $this->assetsResolved[$id] = new $id($this);
            } else {
                $this->assetsResolved[$id] = isset($this->assetsResolved[spl_object_hash($this)][$id]) ? $this->assetsResolved[spl_object_hash($this)][$id] : null;
            }
        }


        if (!isset($this->assetsResolved[$id])) {
            throw new ContainerNotFoundException($id);
        }

        return $this->assetsResolved[$id];
    }


    public function has($id)
    {
        return isset($this->assets[$id]) || isset($this->assetsResolved[spl_object_hash($this)][$id]);
    }
}
