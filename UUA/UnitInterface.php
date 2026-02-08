<?php

//declare(strict_types=1);

namespace UUA;

interface UnitInterface extends ContainerConstructInterface
{

    /**
     * @return void
     */
    public function serve();
}
