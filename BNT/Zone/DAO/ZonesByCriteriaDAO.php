<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZonesByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    public array $zones;

    #[\Override]
    public function serve(): void
    {
        $this->zones = $this->selectRows('zones');
    }
}
