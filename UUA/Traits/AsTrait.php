<?php

declare(strict_types=1);

namespace UUA\Traits;

trait AsTrait
{
    /**
     * 
     * @param object|null $self
     * @return static
     * @throws \Exception
     */
    public static function as(?object $self): object
    {
        if (is_null($self)) {
            $backtrace = debug_backtrace(2);

            if (isset($backtrace[0]['file'], $backtrace[0]['line'])) {
                throw new \Exception(sprintf('Ожидался %s, а пришел null. Вызывал %s (%s)', static::class, $backtrace[0]['file'], $backtrace[0]['line']));
            }

            throw new \Exception(sprintf('Ожидался %s, а пришел null', static::class));
        }

        if (!($self instanceof static)) {
            throw new \Exception(sprintf('Ожидался %s, а пришел %s', static::class, get_class($self)));
        }

        return $self;
    }

    public static function is(?object $self): bool
    {
        return $self instanceof static;
    }
}
