<?php

declare(strict_types=1);

namespace BNT\Link\Mapper;

use BNT\Link\Entity\Link;
use BNT\ServantInterface;

class LinkMapper implements ServantInterface
{
    public ?array $row = null;
    public ?Link $link = null;

    public function serve(): void
    {
        if (empty($this->link) && !empty($this->row)) {
            $link = $this->link = new Link;
            $link->link_id = intval($this->row['link_id']);
            $link->link_dest = intval($this->row['link_dest']);
            $link->link_start = intval($this->row['link_start']);
        }

        if (!empty($this->link) && empty($this->row)) {
            $link = $this->link;
            $row = [];
            $row['link_id'] = $link->link_id;
            $row['link_dest'] = $link->link_dest;
            $row['link_start'] = $link->link_start;

            $this->row = $row;
        }
    }
}
