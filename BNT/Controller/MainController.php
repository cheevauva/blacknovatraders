<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\Servant\ShipExploreSectorServant;
use BNT\Ship\Exception\ShipExploreSectorNotAllowOnPlanetException;

class MainController extends BaseController
{

    public array $sectorinfo = [];
    public array $links = [];
    public array $planets = [];
    public array $defences = [];
    public array $zoneinfo = [];
    public array $traderoutes = [];
    public array $shipsInSector;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if (!empty(trim(strval($this->playerinfo['cleared_defences'])))) {
            $this->redirectTo($this->playerinfo['cleared_defences']);
            return;
        }

        try {
            $exploreSector = ShipExploreSectorServant::new($this->container);
            $exploreSector->ship = $this->playerinfo;
            $exploreSector->serve();

            $this->sectorinfo = $exploreSector->sector;
            $this->links = $exploreSector->links;
            $this->planets = $exploreSector->planets;
            $this->defences = $exploreSector->sectorDefences;
            $this->zoneinfo = $exploreSector->zone;
            $this->traderoutess = $exploreSector->traderoutes;
            $this->shipsInSector = $exploreSector->ships;

            if (!empty($this->queryParams['demo'])) {
                $this->demoData();
            }

            foreach ($this->shipsInSector as $idx => $shipInSector) {
                $success = sensorsCloakSuccess($this->playerinfo['sensors'], $shipInSector['cloak']);
                $roll = rand(1, 100);

                if ($roll >= $success) {
                    unset($this->shipsInSector[$idx]);
                }
            }

            $this->render('tpls/main.tpl.php');
        } catch (ShipExploreSectorNotAllowOnPlanetException $ex) {
            redirectTo('planet.php?planet_id=' . $ex->planet);
            return;
        }
    }

    protected function demoData(): void
    {
        $this->traderoutes[] = [];
        $this->planets[] = [];
        $this->shipsInSector[] = [];
        $this->defences[] = [];

        for ($i = 0; $i < 10; $i++) {
            $this->traderoutes[] = [
                'traderoute_id' => $i,
            ];
            $this->planets[] = [
                'name' => 'P' . $i,
                'planet_id' => $i,
                'owner_score' => $i * 3,
                'owner' => $i * 1000,
                'owner_ship_name' => 'OSN' . $i * 1000,
            ];
            $this->shipsInSector[] = [
                'ship_id' => $i,
                'score' => $i * 3,
                'ship_name' => 'S' . $i * 1000,
            ];

            $defenceTypes = ['F', 'M'];
            $defenceFmSetting = ['attack', 'toll'];
            $this->defences[] = [
                'ship_name' => 'SN' . $i * 1000,
                'quantity' => rand(0, 100),
                'defence_id' => $i,
                'fm_setting' => $defenceFmSetting[rand(0, 1)],
                'defence_type' => $defenceTypes[rand(0, 1)],
            ];
        }
    }
}
