<?php

use BNT\Ship\Servant\ShipSelfDestructServant;
use BNT\Servant\TransactionServant;

require_once './config.php';

connectdb();
loadlanguage($lang);

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();

switch ($_GET['sure'] ?? null) {
    default:
        echo twig()->render('harakiri/harakiri.twig');
        break;
    case 1:
        echo twig()->render('harakiri/step1.twig');
        break;
    case 2:
        $selfDestruct = new ShipSelfDestructServant;
        $selfDestruct->ship = $playerinfo;
        $selfDestruct->ip = $ip;
        
        TransactionServant::call($selfDestruct);
        header('Location: logout.php');
        die;
        break;
}
