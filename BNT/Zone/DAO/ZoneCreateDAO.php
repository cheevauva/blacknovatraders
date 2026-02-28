<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZoneCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('zones');
    }
}
