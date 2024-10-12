<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\ServantInterface;
use BNT\DAO\TransactionBeginTransactionDAO;
use BNT\DAO\TransactionCommitDAO;
use BNT\DAO\TransactionRollbackDAO;

class TransactionServant implements ServantInterface
{
    public ServantInterface $servant;

    public function serve(): void
    {
        (new TransactionBeginTransactionDAO)->serve();

        try {
            $this->servant->serve();

            (new TransactionCommitDAO)->serve();
        } catch (\Throwable $ex) {
            (new TransactionRollbackDAO)->serve();

            throw $ex;
        }
    }

    public static function call(ServantInterface $servant): void
    {
        $self = new static();
        $self->servant = $servant;
        $self->serve();
    }
}
