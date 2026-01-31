<?php

include("config.php");
include("languages/$lang");

connectdb();

$online = sqlGetOnlinePlayersCount();
$mySEC = 0;

$schedulerData = sqlGetSchedulerLastRun();

if ($schedulerData) {
    $mySEC = ($sched_ticks * 60) - (TIME() - $schedulerData['last_run']);
}
if ($mySEC < 0) {
    $mySEC = ($sched_ticks * 60);
}

echo json_encode([
    'online' => $online,
    'schedTicks' => $sched_ticks,
    'myx' => $mySEC,
]);
