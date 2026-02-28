<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('ships');
    }
}
