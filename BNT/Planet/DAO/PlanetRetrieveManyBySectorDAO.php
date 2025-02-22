<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetRetrieveManyBySectorDAO extends PlanetDAO
{
    public int $sector;
    public array $planets;

    #[\Override]
    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'p');
        $qb->andWhere('p.sector_id = :sector_id');
        $qb->setParameters([
            'sector_id' => $this->sector,
        ]);

        $this->planets = $this->asPlanets($qb->fetchAllAssociative());
    }

    public static function call(\Psr\Container\ContainerInterface $container, int $sector): array
    {
        $self = static::new($container);
        $self->sector = $sector;
        $self->serve();

        return $self->planets;
    }
}
