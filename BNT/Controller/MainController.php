<?php

declare(strict_types=1);

namespace BNT\Controller;

use Exception;
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
    protected function processGet(): void
    {
        if (!empty(trim(strval($this->playerinfo['cleared_defences'])))) {
            $this->redirectTo($this->playerinfo['cleared_defences']);
            return;
        }

        try {
            $entryPointMain = ShipExploreSectorServant::new($this->container);
            $entryPointMain->ship = $this->playerinfo;
            $entryPointMain->serve();

            $this->sectorinfo = $entryPointMain->sector;
            $this->links = $entryPointMain->links;
            $this->planets = $entryPointMain->planets;
            $this->defences = $entryPointMain->sectorDefences;
            $this->zoneinfo = $entryPointMain->zone;
            $this->traderoutess = $entryPointMain->traderoutes;
            $this->shipsInSector = $entryPointMain->ships;

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
        } catch (Exception $ex) {
            $this->exception = $ex;
            $this->render('tpls/error.tpl.php');
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
                'traderoute_id' => $ii,
            ];
            $this->planets[] = [
                'name' => 'P' . $i,
                'planet_id' => $i,
                'owner_score' => $i * 3,
                'owner' => $i * 1000,
                'owner_character_name' => 'OCN' . $i * 1000,
            ];
            $this->shipsInSector[] = [
                'ship_id' => $i,
                'score' => $i * 3,
                'ship_name' => 'S' . $i * 1000,
                'character_name' => 'N' . $i * 1000,
            ];

            $defenceTypes = ['F', 'M'];
            $defenceFmSetting = ['attack', 'toll'];
            $this->defences[] = [
                'character_name' => 'CN' . $i * 1000,
                'quantity' => rand(0, 100),
                'defence_id' => $i,
                'fm_setting' => $defenceFmSetting[rand(0, 1)],
                'defence_type' => $defenceTypes[rand(0, 1)],
            ];
        }
    }
}
