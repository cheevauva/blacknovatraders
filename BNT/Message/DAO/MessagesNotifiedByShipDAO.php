<?php

declare(strict_types=1);

namespace BNT\Message\DAO;

class MessagesNotifiedByShipDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $ship;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q("UPDATE messages SET notified = 'Y' WHERE recp_id = :shipId AND notified = 'N'", [
            'shipId' => $this->ship,
        ]);
    }
}
