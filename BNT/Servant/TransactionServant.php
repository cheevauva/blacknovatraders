<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\ServantInterface;
use BNT\DatabaseTrait;

class TransactionServant implements ServantInterface
{

    use DatabaseTrait;

    public ServantInterface $servant;

    public function serve(): void
    {
        $this->db()->beginTransaction();

        try {
            $this->servant->serve();
            $this->db()->commit();
        } catch (\Throwable $ex) {
            $this->db()->rollBack();

            throw $ex;
        }
    }

    public static function call(ServantInterface $servant): void
    {
        $self = new static;
        $self->servant = $servant;
        $self->serve();
    }

}
