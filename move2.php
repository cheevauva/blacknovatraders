<?php

declare(strict_types=1);

use BNT\Ship\Servant\ShipMoveServant;
use BNT\SectorDefence\Servant\SectorDefenceCheckFightersServant;
use BNT\SectorDefence\Servant\SectorDefencePayTollServant;
use BNT\SectorDefence\Servant\SectorDefenceFightSevant;
use BNT\SectorDefence\Servant\SectorDefenceSneakServant;
use BNT\SectorDefence\Servant\SectorDefenceRetreatServant;
use BNT\SectorDefence\Exception\SectorDefenceHasEmenyException;

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

            try {
                $move = new ShipMoveServant;
                $move->checkFighters = $checkFighters;
                $move->ship = $ship;
                $move->sector = intval($_GET['sector']);
                $move->serve();
                header('Location: main.php');
                die;
            } catch (SectorDefenceHasEmenyException $ex) {
                echo twig()->render('check_fighters.twig', [
                    'fightersToll' => $checkFighters->fightersToll,
                    'totalSectorFighters' => $checkFighters->totalSectorFightes,
                    'sector' => $_GET['sector'],
                    'destination' => $_GET['destination'] ?? 0,
                ]);
            }
            break;
        case 'POST':
            switch ($_POST['response']) {
                case 'fight':
                    $fight = new SectorDefenceFightSevant;
                    $fight->ship = $ship;
                    $fight->sector_id = intval($_GET['sector']);
                    $fight->doIt = false;
                    $fight->serve();
                    echo '<pre>';
                    print_r($fight);
                    die;
                    break;
                case 'retreat':
                    SectorDefenceRetreatServant::call($this->ship);
                    break;
                case 'pay':
                    $pay = new SectorDefencePayTollServant;
                    $pay->ship = $this->ship;
                    $pay->sector = intval($_GET['sector']);
                    $pay->serve();
                    break;
                case 'sneak':
                    try {
                        $sneak = new SectorDefenceSneakServant;
                        $sneak->fightersOwner = $fightersOwner;
                        $sneak->ship = $ship;
                        $sneak->serve();
                    } catch (SectorDefenceDetectYourShipException $ex) {
                        $fight = new SectorDefenceFightSevant;
                        $fight->ship = $ship;
                        $fight->sector_id = intval($_GET['sector']);
                        ;
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

