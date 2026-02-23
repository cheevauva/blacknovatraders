<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipsByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    public array $ships;

    #[\Override]
    public function serve(): void
    {
        $this->ships = $this->selectRows('ships');
    }
}
