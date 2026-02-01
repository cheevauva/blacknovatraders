<?php

include("config.php");
include("languages/$lang");

connectdb();
checklogin(false);

$online = sqlGetOnlinePlayersCount();
$mySEC = 0;

$schedulerData = sqlGetSchedulerLastRun();

if ($schedulerData) {
    $mySEC = ($sched_ticks * 60) - (TIME() - $schedulerData['last_run']);
}
if ($mySEC < 0) {
    $mySEC = ($sched_ticks * 60);
}


$unreadMessages = sqlCheckUnreadMessages($playerinfo);

echo json_encode([
    'online' => $online,
    'schedTicks' => $sched_ticks,
    'myx' => $mySEC,
    'unreadMessages' => $unreadMessages ? $l_youhave . $unreadMessages . $l_messages_wait : null,
], JSON_UNESCAPED_UNICODE);
