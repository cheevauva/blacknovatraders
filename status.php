<?php

use BNT\EntryPoint\Servant\EntryPointStatusServant;

$disableRegisterGlobalFix = true;

include 'config.php';

$entrtyPointStatus = EntryPointStatusServant::_new($container);
$entrtyPointStatus->ship = $playerinfo;
$entrtyPointStatus->serve();

$schedulerLastRun = $entrtyPointStatus->schedulerLastRun;
$mySEC = 0;

if ($schedulerLastRun) {
    $mySEC = ($sched_ticks * 60) - (time() - $schedulerLastRun);
}
if ($mySEC < 0) {
    $mySEC = ($sched_ticks * 60);
}


echo json_encode([
    'online' => $entrtyPointStatus->online,
    'schedTicks' => $sched_ticks,
    'myx' => $mySEC,
    'unreadMessages' => $entrtyPointStatus->messages ? $l_youhave . $entrtyPointStatus->messages . $l_messages_wait : null,
    'M' => sprintf('%.2f', memory_get_peak_usage() / 1024 / 1024, 2),
    'E' => sprintf('%.3f', microtime(true) - MICROTIME_START),
], JSON_UNESCAPED_UNICODE);
