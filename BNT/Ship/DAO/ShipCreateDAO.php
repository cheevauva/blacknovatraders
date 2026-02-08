<?php

//declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public $ship;
    public $id;

    public function serve()
    {
        $this->ship['ship_id'] = $this->id = $this->rowCreate('ships', $this->ship);
    }
}
