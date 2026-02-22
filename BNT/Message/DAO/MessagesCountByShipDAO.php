<?php

declare(strict_types=1);

namespace BNT\Message\DAO;

class MessagesCountByShipDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $count;
    public $ship;

    #[\Override]
    public function serve(): void
    {
        $this->count = $this->db()->column("SELECT COUNT(*) FROM messages WHERE recp_id = :shipId AND notified = 'N'", [
            'shipId' => $this->ship,
        ]);
    }
}
