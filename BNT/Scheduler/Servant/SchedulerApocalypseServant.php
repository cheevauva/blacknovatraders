<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Planet\DAO\PlanetsByCriteriaDAO;
use BNT\DB\Criteria\CriteriaMoreThan;
use BNT\Planet\DAO\PlanetUpdateDAO;

/**
 * The four horsemen of the apocalypse set forth
 */
class SchedulerApocalypseServant extends \UUA\Servant
{
    use \BNT\Traits\UnitSimpleCallTrait;
    
    #[\Override]
    public function serve(): void
    {
        global $doomsday_value;
        global $space_plague_kills;

        $planets = PlanetsByCriteriaDAO::call($this->container, [
            'colonists' => new CriteriaMoreThan($doomsday_value),
        ])->planets;

        $chance = 9;
        $reccount = count($planets);

        if ($reccount > 200) {
            $chance = 7; // increase chance it will happen if we have lots of planets meeting the criteria
        }

        $affliction = rand(1, $chance); // the chance something bad will happen

        if ($affliction < 3 && $reccount > 0) {
            $planet = $planets[rand(1, $reccount)];
 
            if ($affliction == 1) {
                // The horsmen release the Space Plague!
                PlanetUpdateDAO::call($this->container, [
                    'colonists' => round($planet['colonists'] - ($planet['colonists'] * $space_plague_kills)),
                ], $planet['planet_id']);

                LogPlayerDAO::call($this->container, $planet['owner'], LogTypeConstants::LOG_SPACE_PLAGUE, [
                    $planet['name'],
                    $planet['sector_id'],
                    round($space_plague_kills * 100)
                ]);
            } else {
                // The horsemen release a Plasma Storm!
                PlanetUpdateDAO::call($this->container, [
                    'energy' => 0,
                ], $planet['planet_id']);

                LogPlayerDAO::call($this->container, $planet['owner'], LogTypeConstants::LOG_PLASMA_STORM, [
                    $planet['name'],
                    $planet['sector_id'
                ]]);
            }
        }
    }
}
