<?php

declare(strict_types=1);

namespace BNT\User\DAO;

class UserCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('users');
    }
}
