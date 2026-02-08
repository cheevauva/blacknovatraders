<?php

include 'config.php';

$online = BNT\ShipFunc::shipsGetOnlinePlayersCount();
$mySEC = 0;

$schedulerLastRun = schedulerGetLastRun();

if ($schedulerLastRun) {
    $mySEC = ($sched_ticks * 60) - (TIME() - $schedulerLastRun);
}
if ($mySEC < 0) {
    $mySEC = ($sched_ticks * 60);
}

$unreadMessages = 0;

if ($playerinfo) {
    $unreadMessages = messagesCountByShip($playerinfo['ship_id']);

    if ($unreadMessages > 0) {
        messagesNotifiedByShip($playerinfo['ship_id']);
    }
}

echo json_encode([
    'online' => $online,
    'schedTicks' => $sched_ticks,
    'myx' => $mySEC,
    'unreadMessages' => $unreadMessages ? $l_youhave . $unreadMessages . $l_messages_wait : null,
    'M' => sprintf('%.2f',memory_get_peak_usage() / 1024 / 1024, 2),
    'E' => sprintf('%.3f', microtime(true) - MICROTIME_START),
], JSON_UNESCAPED_UNICODE);
