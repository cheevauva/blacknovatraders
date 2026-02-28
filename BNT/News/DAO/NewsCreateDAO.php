<?php

declare(strict_types=1);

namespace BNT\News\DAO;

class NewsCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('news');
    }
}
