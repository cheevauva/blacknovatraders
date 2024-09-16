<?php

declare(strict_types=1);

use BNT\Servant\SectorDefenceCheckFightersServant;
use BNT\Ship\Servant\ShipMoveServant;

require_once './config.php';
loadlanguage($lang);

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $checkFighters = new SectorDefenceCheckFightersServant;
            $checkFighters->sector = intval($_GET['sector']);
            $checkFighters->ship = $ship;
            $checkFighters->serve();
            
            if (!$checkFighters->hasEnemy) {
                $move=  new ShipMoveServant;
                $move->ship = $ship;
                $move->doIt = true;
                $move->sector = intval($_GET['sector']);
                $move->serve();
                header('Location: main.php');
                die;
            }

            echo twig()->render('check_fighters.twig', [
                'fightersToll' => $checkFighters->fightersToll,
                'totalSectorFighters' => $checkFighters->totalSectorFightes,
                'sector' => $_GET['sector'],
                'destination' => $_GET['destination'] ?? 0,
            ]);
            break;
        case 'POST':
            switch ($_GET['response']) {
                case 'fight':
                    $fight = new SectorDefenceFightSevant;
                    $fight->ship = $this->ship;
                    $fight->sector_id = $sectorObj->sector_id;
                    $fight->serve();

                    break;
                case 'retreat':
                    SectorDefenceRetreatServant::call($this->ship);
                    break;
                case 'pay':
                    $pay = new SectorDefencePayTollServant;
                    $pay->ship = $this->ship;
                    $pay->sector = $this->sector;
                    $pay->serve();
                    break;
                case 'sneak':
                    try {
                        $sneak = new SectorDefenceSneakServant;
                        $sneak->fightersOwner = $fightersOwner;
                        $sneak->ship = $this->ship;
                        $sneak->serve();
                    } catch (SectorDefenceDetectYourShipException $ex) {
                        $fight = new SectorDefenceFightSevant;
                        $fight->ship = $this->ship;
                        $fight->sector_id = $sectorObj->sector_id;
                        $fight->serve();
                        // todo
                    }

                    break;
            }

            print_r($_POST);
            die;
            break;
    }
} catch (Exception $ex) {
    echo twig()->render('error.twig', [
        'error' => $ex->getMessage(),
    ]);
}

