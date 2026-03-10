<?php

declare(strict_types=1);

namespace BNT\MovementLog\DAO;

class MovementLogLastShipInSectorDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public ?int $excludeShip;
    public mixed $ship;
    public int $sector;

    #[\Override]
    public function serve(): void
    {
        $this->ship = $this->db()->fetchColumn('SELECT ship_id FROM movement_log WHERE ship_id != :excludeShip AND sector_id = :sector ORDER BY time DESC LIMIT 1', [
            'excludeShip' => $this->excludeShip,
            'sector' => $this->sector
        ]);
    }
}
