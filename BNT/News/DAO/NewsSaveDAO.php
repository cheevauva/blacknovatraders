<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use BNT\News\News;

class NewsSaveDAO extends NewsDAO
{

    public News $news;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->news = $this->news;
        $mapper->serve();

        $this->db()->update($this->table(), $mapper->row, [
            'news_id' => $this->news->news_id,
        ]);
    }

    public static function call(News $news): void
    {
        $self = new static;
        $self->news = $news;
        $self->serve();
    }

}
