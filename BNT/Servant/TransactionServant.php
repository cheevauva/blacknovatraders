<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\Servant;
use BNT\DAO\TransactionBeginTransactionDAO;
use BNT\DAO\TransactionCommitDAO;
use BNT\DAO\TransactionRollbackDAO;

class TransactionServant extends Servant
{
    public Servant $servant;

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

    public static function call(\Psr\Container\ContainerInterface $container, Servant $servant): void
    {
        $self = static::new($container);
        $self->servant = $servant;
        $self->serve();
    }
}
