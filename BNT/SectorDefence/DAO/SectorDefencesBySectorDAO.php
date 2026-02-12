<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefencesBySectorDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $sector;
    public $sectorDefences;

    public function serve(): void
    {
        $sql = "
        SELECT 
            sector_defence.*,
            ships.character_name
        FROM
            sector_defence,
            ships
        WHERE 
            sector_defence.sector_id = :sectorId AND 
            ships.ship_id = sector_defence.ship_id 
        ";

        $this->sectorDefences = $this->db()->fetchAll($sql, [
            'sectorId' => $this->sector,
        ]);
    }
}
