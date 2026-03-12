<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefencesCleanUpDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q('DELETE FROM sector_defence WHERE quantity <= 0');
    }
}
