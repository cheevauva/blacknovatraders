<?php

declare(strict_types=1);

namespace BNT\User\DAO;

class UserByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    public ?array $user;

    #[\Override]
    public function serve(): void
    {
        $this->user = $this->selectRow('users', 'id');
    }
}
