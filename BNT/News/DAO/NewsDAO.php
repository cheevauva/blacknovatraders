<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use BNT\Enum\TableEnum;
use BNT\News\Mapper\NewsMapper;
use BNT\News\Entity\News;
use BNT\DAO;


abstract class NewsDAO extends DAO
{
    

    protected function table(): string
    {
        return TableEnum::News->toDb();
    }

    protected function mapper(): NewsMapper
    {
        return new NewsMapper;
    }

    protected function asNews(array $row): News
    {
        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->news;
    }

    protected function asManyNews(array $rows): array
    {
        $news = [];
        
        foreach ($rows as $row) {
            $news[] = $this->asNews($row);
        }
        
        return $news;
    }

    protected function asRow(News $news): array
    {
        $mapper = $this->mapper();
        $mapper->news = $news;
        $mapper->serve();

        return $mapper->row;
    }
}
