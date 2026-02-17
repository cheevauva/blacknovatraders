<?php

include 'config.php';

if (checkship()) {
    die;
}

setcookie("token", uuidv7(), 0, $gamepath, $gamedomain);
setcookie("id", "", 0);
setcookie("res", "", 0);

$current_score = gen_score($playerinfo['ship_id']);
playerlog($playerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_LOGOUT, $ip);

redirectTo('index.php');
