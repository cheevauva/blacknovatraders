<?php

use BNT\Ship\View\ShipView;
use BNT\Log\View\LogView;
use BNT\Log\DAO\LogRetrieveManyByShipDAO;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

$mode = strval($_GET['mode'] ?? 'compat');
$screenres = intval($_GET['screenres'] ?? 1920);
$startdate = new \DateTimeImmutable($_GET['startdate'] ?? 'now');
$yesterday = $startdate->sub(new \DateInterval('P1D'));
$yesterday2 = $startdate->sub(new \DateInterval('P2D'));
$backlink = $startdate->sub(new \DateInterval('P1D'));
$nextlink = $startdate->add(new \DateInterval('P1D'));
$backlinkFull = $startdate->sub(new \DateInterval('P3D'));
$nextlinkFull = $startdate->add(new \DateInterval('P3D'));

$logPacks = [];

$logByShipAndStartDate = new LogRetrieveManyByShipDAO;
$logByShipAndStartDate->time = $startdate;
$logByShipAndStartDate->ship_id = $ship->ship_id;
$logByShipAndStartDate->serve();

$logPacks[] = [
    'date' => $startdate,
    'logs' => LogView::map($logByShipAndStartDate->logs),
];

if ($mode !== 'compat') {
    $logByShipAndYesterday = new LogRetrieveManyByShipDAO;
    $logByShipAndYesterday->time = $yesterday;
    $logByShipAndYesterday->ship_id = $ship->ship_id;
    $logByShipAndYesterday->serve();

    $logByShipAndYesterday2 = new LogRetrieveManyByShipDAO;
    $logByShipAndYesterday2->time = $yesterday2;
    $logByShipAndYesterday2->ship_id = $ship->ship_id;
    $logByShipAndYesterday2->serve();

    $logPacks[] = [
        'date' => $yesterday,
        'logs' => LogView::map($logByShipAndYesterday->logs),
    ];

    $logPacks[] = [
        'date' => $yesterday2,
        'logs' => LogView::map($logByShipAndYesterday2->logs),
    ];
}

echo twig()->render('log/log.twig', [
    'mode' => $mode,
    'ship' => $ship,
    'startdate' => $startdate,
    'backlink' => $backlink,
    'nextlink' => $nextlink,
    'backlinkFull' => $backlinkFull,
    'nextlinkFull' => $nextlinkFull,
    'logPacks' => $logPacks,
    'playerinfo' => new ShipView($ship),
]);
