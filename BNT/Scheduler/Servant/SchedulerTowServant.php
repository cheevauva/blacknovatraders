<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\MovementLog\DAO\MovementLogDAO;
use BNT\Game\DAO\GameBiggerPlayersInRestrictedZonesDAO;

/**
 * Towing bigger players out of restricted zones
 */
class SchedulerTowServant extends SchedulerServant
{

    use \BNT\Traits\UnitSimpleCallTrait;

    #[\Override]
    public function serve(): void
    {
        global $sector_max;

        $numToTow = 0;

        do {
            $rows = GameBiggerPlayersInRestrictedZonesDAO::call($this->container)->rows;

            $numToTow = count($rows);

            foreach ($rows as $row) {
                $newsector = rand(0, $sector_max);

                ShipUpdateDAO::call($this->container, [
                    'sector' => $newsector,
                    'cleared_defences' => '',
                ], $row['ship_id']);

                LogPlayerDAO::call($this->container, $row['ship_id'], LogTypeConstants::LOG_TOW, [$row['sector'], $newsector, $row['max_hull']]);
                MovementLogDAO::call($this->container, $row['ship_id'], $newsector);
            }
        } while ($numToTow);
    }
}
