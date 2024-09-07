<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefenceRemoveByCriteriaDAO extends SectorDefenceDAO
{

    public ?int $ship_id;

    public function serve(): void
    {
        $criteria = [];

        if (isset($this->ship_id)) {
            $criteria['ship_id'] = $this->ship_id;
        }

        $this->db()->delete($this->table(), $criteria);
    }

}
