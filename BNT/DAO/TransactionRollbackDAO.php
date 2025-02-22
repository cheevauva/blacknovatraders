<?php

declare(strict_types=1);

namespace BNT\DAO;

class TransactionRollbackDAO implements \BNT\DAO
{
    use \BNT\Traits\DatabaseTrait;

    public function serve(): void
    {
        $this->db()->rollBack();
    }
}
