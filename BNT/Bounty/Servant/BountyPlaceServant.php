<?php

declare(strict_types=1);

namespace BNT\Bounty\Servant;

use BNT\Exception\WarningException;
use BNT\Bounty\DAO\BountyCreateDAO;
use BNT\Ship\DAO\ShipByIdDAO;

class BountyPlaceServant extends \UUA\Servant
{

    public int $amount;
    public int $placedBy;
    public int $bountyOn;

    #[\Override]
    public function serve(): void
    {
        global $bounty_maxvalue;

        $placedBy = ShipByIdDAO::call($this->container, $this->placedBy)->ship;
        $bountyOn = ShipByIdDAO::call($this->container, $this->bountyOn)->ship;
        
        if ($this->bountyOn == $this->placedBy) {
            throw new WarningException($this->l->by_yourself);
        }
        
        if (!$bountyOn) {
            throw new WarningException($this->l->by_notexists);
        }

        if ($bountyOn['ship_destroyed'] == 'Y') {
            throw new WarningException($this->l->by_destroyed);
        }

        if (empty($this->amount)) {
            throw new WarningException($this->l->by_zeroamount);
        }

        if ($this->amount > $placedBy['credits']) {
            throw new WarningException($this->l->by_notenough);
        }

        if ($bounty_maxvalue != 0) {
            $percent = $bounty_maxvalue * 100;
            $score = gen_score($placedBy['ship_id']);
            $maxtrans = $score * $score * $bounty_maxvalue;

            $previous_bounty = 0;
            $prev = db()->fetch("SELECT SUM(amount) AS totalbounty FROM bounty WHERE bounty_on = :bounty_on AND placed_by = :placed_by", [
                'bounty_on' => $this->bountyOn,
                'placed_by' => $placedBy['ship_id']
            ]);

            if ($prev) {
                $previous_bounty = $prev['totalbounty'];
            }

            if ($this->amount + $previous_bounty > $maxtrans) {
                throw new WarningException($this->l->by_toomuch);
            }
        }

        BountyCreateDAO::call($this->container, [
            'bounty_on' => $this->bountyOn,
            'placed_by' => $this->placedBy,
            'amount' => $this->amount
        ]);
    }
}
