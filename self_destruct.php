<?php

use BNT\Ship\Servant\ShipSelfDestructServant;

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
        ShipSelfDestructServant::call($playerinfo, $ip);
        header('Location: logout.php');
        die;
        break;
}
