<?php

declare(strict_types=1);

namespace BNT\Bounty\Mapper;

use BNT\Bounty\Entity\Bounty;

class BountyMapper implements \BNT\ServantInterface
{
    public Bounty $bounty;
    public array $row;

    public function serve(): void
    {
        if (empty($this->bounty) && !empty($this->row)) {
            $bounty = $this->bounty = new Bounty;
            $bounty->bounty_id = intval($this->row['bounty_id']);
            $bounty->bounty_on = $this->row['bounty_on'];
            $bounty->placed_by = $this->row['placed_by'];
            $bounty->amount = intval($this->row['amount']);
        }

        if (!empty($this->bounty) && empty($this->row)) {
            $bounty = $this->bounty;
            $row = [];
            $row['bounty_id'] = $bounty->bounty_id ?? null;
            $row['bounty_on'] = $bounty->bounty_on;
            $row['placed_by'] = $bounty->placed_by;
            $row['amount'] = $bounty->amount;

            $this->row = $row;
        }
    }
}
