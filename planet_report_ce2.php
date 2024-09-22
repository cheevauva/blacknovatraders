<?php

declare(strict_types=1);

use BNT\Planet\DAO\PlanetRetrieveManyByCriteria;
use BNT\Planet\Entity\Planet;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

echo '<pre>';
print_r($_POST);