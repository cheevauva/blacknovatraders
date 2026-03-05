<?php

declare(strict_types=1);

namespace BNT\User\DAO;

class UsersByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    public array $users;

    #[\Override]
    public function serve(): void
    {
        $this->users = $this->selectRows('users');
    }
}
