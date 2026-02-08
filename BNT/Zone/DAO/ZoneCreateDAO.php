<?php

//declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZoneCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public $zone;
    public $id;

    public function serve()
    {
        $this->id = $this->zone['zone_id'] = $this->rowCreate('zones', $this->zone);
    }
}
