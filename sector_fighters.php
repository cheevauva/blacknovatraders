<?php

use BNT\Sector\Exception\SectorFightShipDestroyedException;

preg_match("/sector_fighters.php/i", $_SERVER['PHP_SELF']) ? die('You can not access this file directly!') : null;

if (!isset($total_sector_fighters)) {
    throw new \Exception('total_sector_fighters is required');
}

if (!isset($playerinfo)) {
    throw new \Exception('playerinfo is required');
}

if (!isset($sector)) {
    throw new \Exception('sector is required');
}
