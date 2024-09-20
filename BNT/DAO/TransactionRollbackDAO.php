<?php

declare(strict_types=1);

namespace BNT\DAO;

class TransactionRollbackDAO implements \BNT\ServantInterface
{

    use \BNT\DatabaseTrait;

    public function serve(): void
    {
        $this->db()->rollBack();
    }

}
