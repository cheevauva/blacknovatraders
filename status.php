<?php

include 'config.php';



checklogin(false);

$online = shipsGetOnlinePlayersCount();
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
], JSON_UNESCAPED_UNICODE);
