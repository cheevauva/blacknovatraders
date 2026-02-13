<?php

declare(strict_types=1);

namespace BNT\Traits;

trait UnitSimpleCallTrait
{
    /**
     * @param type $container
     * @return self
     */
    public static function call($container)
    {
        $self = self::new($container);
        $self->serve();

        return $self;
    }
}
