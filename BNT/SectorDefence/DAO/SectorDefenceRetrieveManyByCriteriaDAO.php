<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

use BNT\SectorDefence\SectorDefenceTypeEnum;

class SectorDefenceRetrieveManyByCriteriaDAO extends SectorDefenceDAO
{

    public ?int $sector_id;
    public ?SectorDefenceTypeEnum $defence_type;
    public array $defences = [];
    public ?bool $orderByQuantityDESC;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->sector_id)) {
            $qb->andWhere('sector_id = :sector_id');
            $qb->setParameters('sector_id', $this->sector_id);
        }

        if (isset($this->defence_type)) {
            $qb->andWhere('defence_type = :defence_type');
            $qb->setParameters('defence_type', $this->defence_type->value());
        }

        if (!empty($this->orderByQuantityDESC)) {
            $qb->orderBy('quantity', 'DESC');
        }

        $this->defences = [];

        foreach ($qb->fetchAllAssociative() as $sectorDefence) {
            $mapper = $this->mapper();
            $mapper->row = $sectorDefence;
            $mapper->serve();

            $this->defences[] = $mapper->defence;
        }
    }

}
