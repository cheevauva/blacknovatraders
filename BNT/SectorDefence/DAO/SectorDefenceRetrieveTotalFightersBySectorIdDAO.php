<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefenceRetrieveTotalFightersBySectorIdDAO extends SectorDefenceDAO
{
    public int $sectorId;
    public int $totalFighters;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('SUM(quantity)');
        $qb->from($this->table());
        $qb->andWhere('sector_id = :sector_id');
        $qb->setParameter('sector_id', $this->sectorId);

        $this->totalFighters = (int) $qb->fetchOne();
    }

    public static function call(int $sectorId): int
    {
        $self = new static();
        $self->sectorId = $sectorId;
        $self->serve();

        return $self->totalFighters;
    }
}
