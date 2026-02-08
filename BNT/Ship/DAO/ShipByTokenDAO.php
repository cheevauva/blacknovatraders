<?php

//declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipByTokenDAO extends \UUA\DAO
{

    /**
     * @var string
     */
    public $token;

    /**
     * @var array
     */
    public $ship;

    public function serve()
    {
        $this->ship = db()->fetch("SELECT * FROM ships WHERE token = :token LIMIT 1", [
            'token' => $this->token,
        ]);
    }
}
