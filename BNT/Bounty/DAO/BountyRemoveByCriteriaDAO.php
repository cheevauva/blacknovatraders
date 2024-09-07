<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

class BountyRemoveByCriteriaDAO extends BountyDAO
{

    public ?int $placedBy;
    public ?int $bountyOn;

    public function serve(): void
    {
        $criteria = [];
        
        if (isset($this->bountyOn)) {
            $criteria['bounty_on'] = $this->bountyOn;
        }
        
        if (isset($this->placedBy)) {
            $criteria['placed_by'] = $this->placedBy;
        }

        $this->db()->delete($this->table(), $criteria);
    }

}
