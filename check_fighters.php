<?php

use BNT\Sector\Exception\SectorChooseMoveException;
use BNT\Sector\Exception\SectorFightException;
use BNT\Sector\Exception\SectorRetreatException;
use BNT\Sector\Exception\SectorNotEnoghtCreditsTollException;

preg_match("/check_fighters.php/i", $_SERVER['PHP_SELF']) ? die('You can not access this file directly!') : null;

if (!isset($sector)) {
    throw new \Exception('sector is required');
}

$sectorinfo = sectoryById($sector);
$defencesBySector = defencesBySectorAndFighters($sector);

$i = 0;
$total_sector_fighters = 0;
$owner = true;

$defences = [];

foreach ($defencesBySector as $defence) {
    $defences[] = $defence;
    $total_sector_fighters += $defence['quantity'];

    if ($defence['ship_id'] != $playerinfo['ship_id']) {
        $owner = false;
    }
}

$num_defences = count($defencesBySector);

$isProblem = $num_defences > 0 && $total_sector_fighters > 0 && !$owner;

if (!$isProblem) {
    return;
}
// find out if the fighter owner and player are on the same team
// All sector defences must be owned by members of the same team
$fm_owner = $defences[0]['ship_id'];
$fighters_owner = shipById($fm_owner);
$isProblem = $fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0;

if (!$isProblem) {
    return;
}

$response = fromPost('response');
$message = null;

switch ($response) {
    case 'fight':
        shipResetClearedDefences($playerinfo['ship_id']);
        throw new SectorFightException();
    case 'retreat':
        shipResetClearedDefences($playerinfo['ship_id']);
        shipRetreatToSector($playerinfo['ship_id'], $playerinfo['sector']);
        throw new SectorRetreatException($l_chf_youretreatback);
        break;
    case 'pay':
        shipResetClearedDefences($playerinfo['ship_id']);

        $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;

        if ($playerinfo['credits'] < $fighterstoll) {
            shipToSector($playerinfo['ship_id'], $playerinfo['sector']);
            throw new SectorNotEnoghtCreditsTollException($l_chf_notenoughcreditstoll . "\n" . $l_chf_movefailed);
        } else {
            $tollstring = NUMBER($fighterstoll);
            shipCreditsSub($playerinfo['ship_id'], $fighterstoll);
            distribute_toll($sector, $fighterstoll, $total_sector_fighters);
            playerlog($playerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_TOLL_PAID, "$tollstring|$sector");
        }
        break;
    case 'sneak':
        shipResetClearedDefences($playerinfo['ship_id']);

        if (rand(1, 100) < sensorsCloakSuccess($fighters_owner['sensors'], $playerinfo['cloak'])) {
            throw new SectorFightException($l_chf_thefightersdetectyou);
        }
        break;
    default:
        $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
        shipSetClearedDefences($playerinfo['ship_id'], $calledfrom . '?sector=' . $sector . '&destination=' . $destination . '&engage=' . $engage);
        throw new SectorChooseMoveException();
}

defencesCleanUp();
