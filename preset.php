<?php

use BNT\Ship\Servant\ShipPresetServant;

require './config.php';

connectdb();
loadlanguage($lang);

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();

if (!isset($_POST['change'])) {
    echo twig()->render('preset/preset.twig', [
        'playerinfo' => $playerinfo,
    ]);
} else {
    $message = null;
    
    try {
        $preset = ShipPresetServant::new($container);
        $preset->ship = $playerinfo;
        $preset->preset1 = intval(abs($_POST['preset1'] ?? 0));
        $preset->preset2 = intval(abs($_POST['preset2'] ?? 0));
        $preset->preset3 = intval(abs($_POST['preset3'] ?? 0));
        $preset->serve();
    } catch (\Exception $ex) {
        $message = $ex->getMessage();
    }


    echo twig()->render('preset/preset2.twig', [
        'playerinfo' => $playerinfo,
        'message' => $message,
    ]);
}



