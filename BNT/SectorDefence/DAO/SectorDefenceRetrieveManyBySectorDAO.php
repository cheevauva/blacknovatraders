<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefenceRetrieveManyBySectorDAO extends SectorDefenceDAO
{

    public int $sector;
    public array $defences = [];

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'sd');
        $qb->andWhere('sd.sector_id = :sector_id');
        $qb->setParameters([
            'sector_id' => $this->sector,
        ]);
        
        $this->defences = [];

        foreach ($qb->fetchAllAssociative() as $sectorDefence) {
            $mapper = $this->mapper();
            $mapper->row = $sectorDefence;
            $mapper->serve();

            $this->defences[] = $mapper->defence;
        }
    }

    public static function call(int $sector): array
    {
        $self = new static;
        $self->sector = $sector;
        $self->serve();

        return $self->defences;
    }

}
