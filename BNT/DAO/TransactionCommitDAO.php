<?php

declare(strict_types=1);

namespace BNT\DAO;

class TransactionCommitDAO implements \BNT\ServantInterface
{

    use \BNT\Traits\DatabaseTrait;

    public function serve(): void
    {
        $this->db()->commit();
    }

}
