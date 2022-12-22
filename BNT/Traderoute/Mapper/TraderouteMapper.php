<?php

declare(strict_types=1);

namespace BNT\Traderoute\Mapper;

use BNT\ServantInterface;
use BNT\Traderoute\Traderoute;

class TraderouteMapper implements ServantInterface
{

    public ?array $row = null;
    private ?Traderoute $traderoute = null;

    public function serve(): void
    {
        if (empty($this->traderoute) && !empty($this->row)) {
            $link = $this->traderoute = new Traderoute;
            $link->link_id = intval($this->row['link_id']);
            $link->link_dest = intval($this->row['link_dest']);
            $link->link_start = intval($this->row['link_start']);
        }

        if (!empty($this->traderoute) && empty($this->row)) {
            $link = $this->traderoute;
            $row = [];
            $row['link_id'] = $link->link_id;
            $row['link_dest'] = $link->link_dest;
            $row['link_start'] = $link->link_start;

            $this->row = $row;
        }
    }

}
