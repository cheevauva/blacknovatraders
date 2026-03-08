<?php

declare(strict_types=1);

namespace BNT\Message\DAO;

class MessageByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    public ?array $message;

    #[\Override]
    public function serve(): void
    {
        $this->message = $this->selectRow('messages', 'id');
    }
}
