<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use BNT\TableEnum;
use BNT\News\Mapper\NewsMapper;
use BNT\ServantInterface;
use BNT\DatabaseTrait;

abstract class NewsDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::News->toDb();
    }

    protected function mapper(): NewsMapper
    {
        return new NewsMapper;
    }

}
