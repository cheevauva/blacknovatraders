<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('planets');
    }
}
