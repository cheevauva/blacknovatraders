<?php

//declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipByEmailDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $email;
    public $ship;

    public function serve()
    {
        $this->ship = $this->db()->fetch("SELECT * FROM ships WHERE email = :username LIMIT 1", [
            'username' => $this->email,
        ]);
    }
}
