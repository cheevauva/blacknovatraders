<?php

declare(strict_types=1);

namespace BNT\Traderoute\Mapper;

use BNT\ServantInterface;
use BNT\Traderoute\Traderoute;
use BNT\Traderoute\TraderouteCircuitEnum;
use BNT\Traderoute\TraderouteTypeEnum;
use BNT\Traderoute\TraderouteMoveTypeEnum;

class TraderouteMapper implements ServantInterface
{
    public ?array $row = null;
    public ?Traderoute $traderoute = null;

    public function serve(): void
    {
        if (empty($this->traderoute) && !empty($this->row)) {
            $traderoute = $this->traderoute = new Traderoute;
            $traderoute->traderoute_id = intval($this->row['traderoute_id']);
            $traderoute->owner = intval($this->row['owner']);
            $traderoute->source_id = intval($this->row['source_id']);
            $traderoute->source_type = TraderouteTypeEnum::tryFrom($this->row['source_type']);
            $traderoute->dest_id = intval($this->row['dest_id']);
            $traderoute->dest_type = TraderouteTypeEnum::tryFrom($this->row['dest_type']);
            $traderoute->circuit = TraderouteCircuitEnum::tryFrom($this->row['circuit']);
            $traderoute->move_type = TraderouteMoveTypeEnum::tryFrom($this->row['move_type']);
        }

        if (!empty($this->traderoute) && empty($this->row)) {
            $traderoute = $this->traderoute;
            $row = [];
            $row['link_id'] = $traderoute->link_id;
            $row['link_dest'] = $traderoute->link_dest;
            $row['link_start'] = $traderoute->link_start;

            $this->row = $row;
        }
    }
}
