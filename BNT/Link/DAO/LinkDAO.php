<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use BNT\DAO;

use BNT\Link\Mapper\LinkMapper;
use BNT\Enum\TableEnum;
use BNT\Link\Entity\Link;

abstract class LinkDAO extends DAO
{
    

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
