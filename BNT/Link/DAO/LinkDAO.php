<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\Link\Mapper\LinkMapper;

abstract class LinkDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        global $dbtables;

        return $dbtables['links'];
    }

    protected function mapper(): LinkMapper
    {
        return new LinkMapper;
    }

}
