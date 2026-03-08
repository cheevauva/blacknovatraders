<?php

declare(strict_types=1);

namespace BNT\Message\DAO;

class MessagesDeleteByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsDeleteByCriteria;

    #[\Override]
    public function serve(): void
    {
        $this->deleteRows('messages');
    }
}
