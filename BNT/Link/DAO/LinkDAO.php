<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\Link\Mapper\LinkMapper;
use BNT\TableEnum;

abstract class LinkDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Links->toDb();
    }

    protected function mapper(): LinkMapper
    {
        return new LinkMapper;
    }

}
