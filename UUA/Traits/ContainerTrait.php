<?php

//declare(strict_types=1);

namespace UUA\Traits;

use Psr\Container\ContainerInterface;

trait ContainerTrait
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->init();
    }

    /**
     * @return void
     */
    protected function init()
    {
        
    }
}
