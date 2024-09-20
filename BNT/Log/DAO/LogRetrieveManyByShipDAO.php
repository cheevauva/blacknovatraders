<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use BNT\Ship\Ship;

class LogRetrieveManyByShipDAO extends LogDAO
{
    public Ship $ship;
    public array $logs = [];

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'l');
        $qb->andWhere('l.ship_id = :ship_id');
        $qb->setParameters([
            'ship_id' => $this->ship->ship_id,
        ]);

        $this->logs = $this->asLogs($qb->fetchAllAssociative() ?: []);
    }

    public static function call(Ship $ship): array
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self->logs;
    }
}
