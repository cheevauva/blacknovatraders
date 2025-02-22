<?php

declare(strict_types=1);

namespace BNT;

abstract class DAO implements UnitInterface
{

    use Traits\AsTrait;
    use Traits\BuildTrait;
    use Traits\DatabaseTrait;
}
