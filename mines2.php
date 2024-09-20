<?php

declare(strict_types=1);

use BNT\SectorDefence\Servant\SectorDefenceDeployCheckServant;
use BNT\SectorDefence\Servant\SectorDefenceDeployServant;
use BNT\SectorDefence\Enum\SectorDefenceFmSettingEnum;
use BNT\Servant\TransactionServant;

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
            $deployCheck = new SectorDefenceDeployCheckServant;
            $deployCheck->ship = $ship;
            $deployCheck->quite = false;
            $deployCheck->serve();

            echo twig()->render('mines/form.twig', [
                'ship' => $ship,
                'sector' => $deployCheck->sector,
                'defenceMine' => $deployCheck->defenceMine,
                'defenceFighter' => $deployCheck->defenceFighter,
            ]);
            break;
        case 'POST':
            $deploy = new SectorDefenceDeployServant;
            $deploy->ship = $ship;
            $deploy->numfighters = intval($_POST['numfighters'] ?? 0);
            $deploy->nummines = intval($_POST['nummines'] ?? 0);
            $deploy->mode = SectorDefenceFmSettingEnum::from(strval($_POST['mode'] ?? ''));
            $deploy->doIt = true;

            TransactionServant::call($deploy);
            header('Location: mines2.php');
            break;
    }
} catch (\Throwable $ex) {
    echo twig()->render('error.twig', [
        'error' => $ex->getMessage(),
    ]);
}

die;
