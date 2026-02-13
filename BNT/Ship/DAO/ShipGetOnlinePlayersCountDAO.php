<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipGetOnlinePlayersCountDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public $count;
    public function serve(): void
    {
        $this->count = (int) $this->db()->column("SELECT COUNT(*) as loggedin FROM ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'");
    }
}
