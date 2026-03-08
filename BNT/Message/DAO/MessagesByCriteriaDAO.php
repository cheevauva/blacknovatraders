<?php

declare(strict_types=1);

namespace BNT\Message\DAO;

class MessagesByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    public array $messages;

    #[\Override]
    public function serve(): void
    {
        $this->messages = $this->selectRows('messages');
    }
}
