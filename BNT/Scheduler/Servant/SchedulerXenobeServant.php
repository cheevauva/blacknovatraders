<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Xenobe\Servant\XenobeRegenServant;
use BNT\Xenobe\Servant\XenobeToShipServant;
use BNT\Xenobe\Servant\XenobeMoveServant;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Xenobe\XenobeConstants;

class SchedulerXenobeServant extends \UUA\Servant
{

    protected int $furcount = 0;
    protected int $furcount0 = 0;
    protected int $furcount0a = 0;
    protected int $furcount1 = 0;
    protected int $furcount1a = 0;
    protected int $furcount2 = 0;
    protected int $furcount2a = 0;
    protected int $furcount3 = 0;
    protected int $furcount3a = 0;
    protected int $furcount3h = 0;

    #[\Override]
    public function serve(): void
    {
        $xenobes = db()->fetchAll("SELECT * FROM ships JOIN xenobe WHERE email = xenobe_id and active = 'Y' and ship_destroyed = 'N' ORDER BY ship_id");

        // Make Xenobe selection
        foreach ($xenobes as $xenobe) {
            $regen = XenobeRegenServant::new($this->container);
            $regen->playerinfo = $xenobe;
            $regen->serve();

            $xenobe = $regen->playerinfo;

            if (mt_rand(1, 5) == 1) { // 20% Chance of not moving at all
                continue;
            }

            if ($xenobe['order_id'] == XenobeConstants::ORDER_ID_SENTINEL) {
                $this->furcount0++;
                $this->sentinel($xenobe);
            }

            if ($xenobe['order_id'] == XenobeConstants::ORDER_ID_ROAM) {
                $this->furcount1++;
                $this->roam($xenobe);
            }
        }
    }

    protected function sentinel(array $playerinfo): void
    {
        // Find a target in my sector, not myself, not on a planet

        $target = db()->fetch("SELECT * FROM ships WHERE sector = :sector AND email != :email AND email NOT LIKE '%@xenobe' AND planet_id = 0 AND ship_id > 1", [
            'sector' => $playerinfo['sector'],
            'email' => $playerinfo['email']
        ]);

        if ($target) {
            return;
        }

        if ($playerinfo['aggression'] == XenobeConstants::AGGRESSION_PEACEFUL) {
            // This Guy Does Nothing But Sit As A Target Himself
            return;
        }

        if ($playerinfo['aggression'] == XenobeConstants::AGGRESSION_ATTACK_SOMETIMES && $playerinfo['ship_fighters'] > $target['ship_fighters']) {
            // Xenobe's only compare number of fighters when determining if they have an attack advantage
            $this->furcount0a++;

            LogPlayerDAO::call($this->container, $playerinfo['ship_id'], LogTypeConstants::LOG_Xenobe_ATTACK, $target['ship_name']);
            XenobeToShipServant::call($this->container, $target['ship_id']);
            return;
        }

        if ($playerinfo['aggression'] == XenobeConstants::AGGRESSION_ATTACK_ALWAYS) {
            $this->furcount0a++;

            LogPlayerDAO::call($this->container, $playerinfo['ship_id'], LogTypeConstants::LOG_Xenobe_ATTACK, $target['ship_name']);
            XenobeToShipServant::call($this->container, $target['ship_id']);

            if ($xenobeisdead > 0) {
                return;
            }
        }
    }

    protected function roam(int $xenobeisdead, array $playerinfo): void
    {
        // Roam to a new sector before doing anything else
        $move = XenobeMoveServant::new($this->container);
        $move->playerinfo = $playerinfo;
        $move->serve();

        // Find a target in my sector, not myself
        $target = db()->fetch("SELECT * FROM ships WHERE sector = :sector and email != :email and ship_id > 1", [
            'sector' => $move->targetSector,
            'email' => $playerinfo['email']
        ]);

        if (!$target) {
            return;
        }

        if ($playerinfo['aggression'] == 0) {            // O = 1 & Aggression = 0 Peaceful O = 1
            // This Guy Does Nothing But Roam Around As A Target Himself
        } elseif ($playerinfo['aggression'] == 1) {        // O = 1 & AGRESSION = 1 ATTACK SOMETIMES
            // Xenobe's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo['ship_fighters'] > $target['ship_fighters'] && $target['planet_id'] == 0) {
                $furcount1a++;
                playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $target['character_name']);
                xenobetoship($target['ship_id']);
                if ($xenobeisdead > 0) {
                    return;
                }
            }
        } elseif ($playerinfo['aggression'] == 2) {        //  O = 1 & AGRESSION = 2 ATTACK ALLWAYS
            $furcount1a++;
            playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $target['character_name']);
            if ($target['planet_id'] != 0) {              // IS ON PLANET
                xenobetoplanet($target['planet_id']);
            } else {
                xenobetoship($target['ship_id']);
            }
            if ($xenobeisdead > 0) {
                return;
            }
        }
    }
}
