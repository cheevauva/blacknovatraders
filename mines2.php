<?php

declare(strict_types=1);

use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\SectorDefence\Servant\SectorDefenceDeployCheckServant;
use BNT\SectorDefence\Servant\SectorDefenceDeployServant;
use BNT\SectorDefence\SectorDefenceFmSettingEnum;
use BNT\Servant\TransactionServant;

require_once './config.php';
loadlanguage($lang);

connectdb();

global $l_mines_nopermit;

if (isNotAuthorized()) {
    die();
}

$ship = ship();
$sector = SectorRetrieveByIdDAO::call($ship->sector);

try {
    $deployCheck = new SectorDefenceDeployCheckServant;
    $deployCheck->ship = $ship;
    $deployCheck->serve();

    $deploy = new SectorDefenceDeployServant;
    $deploy->defenceFighter = $deployCheck->defenceFighter;
    $deploy->defenceMine = $deployCheck->defenceMine;
    $deploy->ship = $ship;

    if (!$deployCheck->allowDefenses) {
        throw new \Exception($l_mines_nopermit);
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $deploy->doIt = false;
            $deploy->serve();

            echo twig()->render('mines/form.twig', [
                'ship' => $ship,
                'sector' => $sector,
                'defenceMine' => $deployCheck->defenceMine,
                'defenceFighter' => $deployCheck->defenceFighter,
            ]);
            break;
        case 'POST':

            $deploy->numfighters = intval($_POST['numfighters'] ?? 0);
            $deploy->nummines = intval($_POST['nummines'] ?? 0);
            $deploy->mode = !empty($_POST['mode']) ? SectorDefenceFmSettingEnum::from($_POST['mode']) : SectorDefenceFmSettingEnum::Toll;
            $deploy->doIt = true;

            TransactionServant::call($deploy);
            header('Location: mines2.php');
            break;
    }
} catch (\Exception $ex) {
    echo twig()->render('error.twig', [
        'error' => $ex->getMessage(),
    ]);
}

die;
