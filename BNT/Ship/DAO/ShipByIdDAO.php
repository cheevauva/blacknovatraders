<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $ship;

    #[\Override]
    public function serve(): void
    {
        $this->ship = $this->selectRow('ships', 'ship_id');
    }
}
