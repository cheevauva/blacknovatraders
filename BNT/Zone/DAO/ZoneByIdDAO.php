<?php

//declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZoneByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $id;
    public $zone;

    public function serve()
    {
        $this->zone = db()->fetch('SELECT * FROM zones WHERE zone_id = :zoneId', [
            'zoneId' => $this->id,
        ]);
    }
}
