<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use BNT\Ship\Entity\Ship;

class LogRetrieveManyByShipDAO extends LogDAO
{
    public ?int $ship_id;
    public ?\DateTimeImmutable $time;
    public array $logs = [];

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'l');
       
        if (isset($this->time)) {
            $qb->andWhere('l.time LIKE :time');
            $qb->setParameter('time', $this->time->format('Y-m-d') . '%');
        }
        
        if (isset($this->ship_id)) {
            $qb->andWhere('l.ship_id = :ship_id');
            $qb->setParameter('ship_id', $this->ship_id);
        }

        $this->logs = $this->asLogs($qb->fetchAllAssociative() ?: []);
    }

    public static function call(Ship $ship): array
    {
        $self = new static;
        $self->ship_id = $ship->ship_id;
        $self->serve();

        return $self->logs;
    }
}
