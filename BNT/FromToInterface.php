<?php

declare(strict_types=1);

namespace BNT;

interface FromToInterface
{

    public function from(object $object): void;

    public function to(object $object): void;
}
