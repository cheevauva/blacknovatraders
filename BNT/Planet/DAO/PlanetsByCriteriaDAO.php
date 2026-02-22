<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetsByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    public array $planets;

    #[\Override]
    public function serve(): void
    {
        $this->planets = $this->selectRows('planets');
    }
}
