<?php

declare(strict_types=1);

namespace BNT\Traderoute\DAO;

use BNT\Traderoute\TraderouteTypeEnum;
use BNT\Ship\Ship;
use BNT\Enum\TableEnum;

class TraderouteRetrieveManyByShipAndSourceTypeDAO extends TraderouteDAO
{

    public ?array $traderoutes = [];
    public TraderouteTypeEnum $sourceType;
    public Ship $ship;

    private function getTraderoutesByShipOnPlanet(): array
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('t.*');
        $qb->from($this->table(), 't');
        $qb->innerJoin('t', TableEnum::Planets->toDb(), 'p', 't.source_id = p.planet_id AND p.sector_id = :sector_id AND p.owner = :owner');
        $qb->andWhere('source_type = :source_type');
        $qb->setParameters([
            'source_type' => $this->sourceType->value,
            'sector_id' => $this->ship->sector,
            'owner' => $this->ship->ship_id,
        ]);

        return $qb->fetchAllAssociative() ?: [];
    }

    /**
     * @todo
     */
    private function getTraderoutesByShip(): array
    {
        return $this->db()->executeQuery("SELECT * FROM {$this->table()} WHERE source_type=:source_type AND source_id=:source_id AND owner=:owner ORDER BY dest_id ASC", [
            'source_type' => $this->sourceType->value,
            'source_id' => $this->ship->sector,
            'owner' => $this->ship->ship_id,
        ])->fetchAllAssociative() ?: [];
    }

    public function serve(): void
    {
        $traderoutes = match ($this->sourceType) {
            TraderouteTypeEnum::Port, TraderouteTypeEnum::Defense => $this->getTraderoutesByShip(),
            TraderouteTypeEnum::Personal, TraderouteTypeEnum::Corperate => $this->getTraderoutesByShipOnPlanet(),
        };

        $this->traderoutes = [];

        foreach ($traderoutes as $link) {
            $mapper = $this->mapper();
            $mapper->row = $link;
            $mapper->serve();

            $this->traderoutes[] = $mapper->traderoute;
        }
    }

    public static function call(Ship $ship, TraderouteTypeEnum $type): array
    {
        $self = new static;
        $self->ship = $ship;
        $self->sourceType = $type;
        $self->serve();
        
        return $self->traderoutes;
    }
}
