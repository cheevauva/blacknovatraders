<?php

declare(strict_types=1);

namespace BNT\Message\DAO;

class MessageCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('messages');
    }
}
