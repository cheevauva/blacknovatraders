<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

/**
 * @todo 
 */
class PlanetRetrieveManyBySectorDAO extends PlanetDAO
{

    public int $sector;
    public array $planets;

    public function serve(): void
    {
        $this->planets = $this->db()->fetchAllAssociative("SELECT * FROM {$this->table()} WHERE sector_id=:sector_id", [
            'sector_id' => $this->sector,
        ]) ?: [];
    }

}
