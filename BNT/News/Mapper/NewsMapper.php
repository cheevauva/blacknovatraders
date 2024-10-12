<?php

declare(strict_types=1);

namespace BNT\News\Mapper;

use BNT\News\Entity\News;

class NewsMapper implements \BNT\ServantInterface
{
    public array $row;
    public ?News $news = null;

    public function serve(): void
    {
        if (empty($this->news) && !empty($this->row)) {
            $news = $this->news = new News();
            $news->news_id = intval($this->row['news_id']);
            $news->headline = $this->row['headline'];
            $news->newstext = $this->row['newstext'];
            $news->user_id = intval($this->row['user_id']);
            $news->date = new \DateTime($this->row['date']);
            $news->news_type = $this->row['news_type'];
        }

        if (!empty($this->news) && empty($this->row)) {
            $news = $this->news;
            $row = [];
            $row['news_id'] = $news->news_id ?? null;
            $row['headline'] = $news->headline;
            $row['newstext'] = $news->newstext;
            $row['user_id'] = $news->user_id;
            $row['date'] = $news->date->format('Y-m-d H:i:s');
            $row['news_type'] = $news->news_type;

            $this->row = $row;
        }
    }
}
