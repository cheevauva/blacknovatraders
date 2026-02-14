<?php

declare(strict_types=1);

namespace BNT\IBankAccount\DAO;

use Psr\Container\ContainerInterface;

class IBankAccountCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    /**
     * 
     * @var array<string, mixed>
     */
    public array $ibackAccount;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('ibank_accounts', $this->ibackAccount);
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $bankAccount
     * @return self
     */
    public static function call(ContainerInterface $container, array $bankAccount): self
    {
        $self = self::new($container);
        $self->ibackAccount = $bankAccount;
        $self->serve();

        return $self;
    }
}
