<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use Psr\Container\ContainerInterface;

class LogsByShipAndDateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public string $date;
    public int $ship;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $logs;

    #[\Override]
    public function serve(): void
    {
        $this->logs = $this->db()->fetchAll('SELECT * FROM logs WHERE ship_id = :ship AND time >= :dateFrom AND time <= :dateTo ORDER BY time DESC, type DESC', [
            'ship' => $this->ship,
            'dateFrom' => new \DateTimeImmutable($this->date)->format('Y-m-d 00:00:00'),
            'dateTo' => new \DateTimeImmutable($this->date)->format('Y-m-d 23:59:59'),
        ]);
    }

    public static function call(ContainerInterface $container, int $ship, string $date): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->date = $date;
        $self->serve();

        return $self;
    }
}
