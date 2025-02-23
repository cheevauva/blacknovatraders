<?php

declare(strict_types=1);

namespace BNT;

abstract class Event implements FromToInterface
{

    use Traits\EventTrait;
    use Traits\AsTrait;
    
    public function from(object $object): void
    {
        // nothing
    }

    public function to(object $object): void
    {
        // nothing
    }

}
