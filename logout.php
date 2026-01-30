<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die;
}

setcookie("token", uuidv7(), 0, $gamepath, $gamedomain);
setcookie("id", "", 0);
setcookie("res", "", 0);

$current_score = gen_score($playerinfo['ship_id']);
playerlog($playerinfo['ship_id'], LOG_LOGOUT, $ip);

header('Location: index.php');
