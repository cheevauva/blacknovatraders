<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;
use Doctrine\DBAL\Connection;

class ShipRetrieveManyByCriteriaDAO extends ShipDAO
{

    public ?int $limit = null;
    public ?int $excludeTeam;
    public ?array $inShips;
    public array $ships;
    public ?Ship $firstOfShip;

    #[\Override]
    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->setMaxResults($this->limit);

        if (isset($this->inShips)) {
            $qb->andWhere('ship_id IN (:inShips)');
            $qb->setParameter('inShips', $this->inShips, Connection::PARAM_INT_ARRAY);
        }

        if (isset($this->excludeTeam)) {
            $qb->andWhere('p.team != :exlcudeTeam');
            $qb->setParameter('exlcudeTeam', $this->excludeTeam);
        }

        $this->ships = $this->asShips($qb->fetchAllAssociative());
        $this->firstOfShip = $this->ships[0] ?? null;
    }

}
