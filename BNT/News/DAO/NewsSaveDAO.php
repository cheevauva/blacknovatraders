<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use BNT\News\Entity\News;

class NewsSaveDAO extends NewsDAO
{
    public News $news;

    public function serve(): void
    {
        if (!isset($this->news->news_id)) {
            $this->db()->insert($this->table(), $this->asRow($this->news));
            
            $this->news->news_id = (int) $this->db()->lastInsertId();
        } else {
            $this->db()->update($this->table(), $this->asRow($this->news), [
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
