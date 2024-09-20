<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use BNT\News\Entity\News;

class NewsSaveDAO extends NewsDAO
{
    public News $news;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->news = $this->news;
        $mapper->serve();

        if (!isset($this->news->news_id)) {
            $this->db()->insert($this->table(), $mapper->row);
            $this->news->news_id = (int) $this->db()->lastInsertId();
        } else {
            $this->db()->update($this->table(), $mapper->row, [
                'news_id' => $this->news->news_id,
            ]);
        }
    }

    public static function call(News $news): void
    {
        $self = new static;
        $self->news = $news;
        $self->serve();
    }
}
