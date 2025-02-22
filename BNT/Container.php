<?php

declare(strict_types=1);

namespace BNT;

use Psr\Container\NotFoundExceptionInterface;

class Container implements \Psr\Container\ContainerInterface
{

    protected array $assets = [];

    public function __construct(array $assets)
    {
        $this->assets = $assets;
    }

    public function get(string $id): mixed
    {
        if (isset($this->assets[$id]) && ($this->assets[$id] instanceof \Closure)) {
            $this->assets[$id] = $this->assets[$id]($this);
        }

        if (!isset($this->assets[$id]) && class_exists($id, true)) {
            $this->assets[$id] = new $id($this);
        }

        if (!isset($this->assets[$id])) {
            throw new class(sprintf('Not found "%s" asset', $id)) extends \Exception implements NotFoundExceptionInterface {
                
            };
        }

        return $this->assets[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->assets[$id]);
    }

}
