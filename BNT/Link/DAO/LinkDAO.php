<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\Link\Mapper\LinkMapper;
use BNT\Enum\TableEnum;
use BNT\Link\Entity\Link;
use BNT\Traits\BuildTrait;

abstract class LinkDAO implements ServantInterface
{
    use DatabaseTrait;
    use BuildTrait;

    protected function table(): string
    {
        return TableEnum::Links->toDb();
    }

    protected function mapper(): LinkMapper
    {
        return new LinkMapper;
    }

    protected function asLink(array $row): Link
    {
        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->link;
    }
}
