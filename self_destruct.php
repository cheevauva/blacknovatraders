<?php

include 'config.php';

if (checklogin()) {
    die();
}

$sure = intval(fromRequest('sure', 0));
try {
    switch (requestMethod()) {
        case 'POST':
            if ($sure !== 2) {
                redirectTo('index.php');
                return;
            }

            db_kill_player($playerinfo['ship_id']);
            cancel_bounty($playerinfo['ship_id']);
            adminlog(\BNT\Log\LogTypeConstants::LOG_ADMIN_HARAKIRI, $playerinfo['character_name'] . '|' . $ip);
            playerlog($playerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_HARAKIRI, $ip);
            redirectTo('index.php');
            break;
        case 'GET':
            include 'tpls/self_destruct.tpl.php';
            break;
    }
} catch (\Exception $ex) {
    echo responseJsonByException($ex);
}
