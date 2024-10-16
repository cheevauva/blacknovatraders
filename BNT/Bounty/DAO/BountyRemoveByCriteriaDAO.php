<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\Traits\BuildTrait;

class BountyRemoveByCriteriaDAO extends BountyDAO
{
    use BuildTrait;

    public ?int $placedBy;
    public ?int $bountyOn;
    public ?int $bounty_id;

    public function serve(): void
    {
        $criteria = [];

        if (isset($this->bountyOn)) {
            $criteria['bounty_on'] = $this->bountyOn;
        }

        if (isset($this->bounty_id)) {
            $criteria['bounty_id'] = $this->bounty_id;
        }

        if (isset($this->placedBy)) {
            $criteria['placed_by'] = $this->placedBy;
        }

        $this->db()->delete($this->table(), $criteria);
    }
}
