<?php

include 'config.php';

if (checklogin()) {
    die();
}

$ship_id = fromGet('ship_id');
$othership = shipById($ship_id);

include 'tpls/ship.tpl.php';

