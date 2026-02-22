<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZoneByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByCriteriaTrait;

    public ?array $zone;

    #[\Override]
    public function serve(): void
    {
        $this->zone = $this->selectRow('zone');
    }
}
