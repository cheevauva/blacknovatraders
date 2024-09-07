<?php

declare(strict_types=1);

namespace BNT\News;

class News
{

    public int $news_id;
    public string $headline;
    public string $newstext;
    public int $user_id;
    public \DateTime $date;
    public string $news_type;

    public function __construct()
    {
        $this->date = new \DateTime;
    }

}
