<?php

declare(strict_types=1);

namespace BNT\DAO;

class TransactionBeginTransactionDAO implements \BNT\DAO
{
    use \BNT\Traits\DatabaseTrait;

    public function serve(): void
    {
        $this->db()->beginTransaction();
    }
}
