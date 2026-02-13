<?php

declare(strict_types=1);

namespace BNT\IBankAccount\DAO;

use Psr\Container\ContainerInterface;

class IBankAccountCreateDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseRowCreateTrait;

    public array $ibackAccount;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('ibank_accounts', $this->ibackAccount);
    }

    public static function call(ContainerInterface $container, array $bankAccount): self
    {
        $self = self::new($container);
        $self->ibackAccount = $bankAccount;
        $self->serve();

        return $self;
    }
}
