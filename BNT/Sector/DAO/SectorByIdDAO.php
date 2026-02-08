<?php

//declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $id;
    public $sector;

    public function serve()
    {
        return $this->db()->fetch('SELECT * FROM universe WHERE sector_id = :sectorId LIMIT 1', [
            'sectorId' => $this->id,
        ]);
    }
}
