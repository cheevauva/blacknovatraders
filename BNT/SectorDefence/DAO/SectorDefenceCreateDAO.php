<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefenceCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('sector_defence');
    }
}
