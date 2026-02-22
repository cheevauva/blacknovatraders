<?php

declare(strict_types=1);

namespace BNT\Scheduler\DAO;

use Psr\Container\ContainerInterface;

class SchedulerCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    /**
     * @var array<string, mixed>
     */
    public array $scheduler;
    public int $id;

    #[\Override]
    public function serve(): void
    {
        $this->id = (int) $this->rowCreate('scheduler', $this->scheduler);
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $zone
     * @return self
     */
    public static function call(ContainerInterface $container, array $scheduler): self
    {
        $self = self::new($container);
        $self->scheduler = $scheduler;
        $self->serve();

        return $self;
    }
}
