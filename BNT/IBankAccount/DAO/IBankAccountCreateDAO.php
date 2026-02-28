<?php

declare(strict_types=1);

namespace BNT\IBankAccount\DAO;

class IBankAccountCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('ibank_accounts');
    }
}
