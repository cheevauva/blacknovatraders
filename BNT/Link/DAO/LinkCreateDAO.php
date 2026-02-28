<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinkCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('links');
    }
}
