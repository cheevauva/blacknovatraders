<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Ship;

class ShipRetrieveManyBySectorDAO extends ShipDAO
{

    public int $sector;
    public ?bool $onPlanet = null;
    public array $ships = [];

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 's');
        $qb->andWhere('s.sector = :sector_id');
        $qb->setParameters([
            'sector_id' => $this->sector,
        ]);

        if (is_bool($this->onPlanet)) {
            $qb->andWhere('on_planet = :on_planet');
            $qb->setParameter('on_planet', $this->onPlanet ? 'Y' : 'N');
        }

        $this->ships = [];

        foreach ($qb->fetchAllAssociative() as $sectorDefence) {
            $mapper = $this->mapper();
            $mapper->row = $sectorDefence;
            $mapper->serve();

            $this->ships[] = $mapper->ship;
        }
    }

    public static function call(int $sector, ?bool $onPlanet = null): array
    {
        $self = new static;
        $self->sector = $sector;
        $self->onPlanet = $onPlanet;
        $self->serve();

        return $self->ships;
    }

}
