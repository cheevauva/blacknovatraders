<?php

declare(strict_types=1);

$events = [];
$events[\BNT\Log\Event\LogEvent::class] = [
    \BNT\Log\DAO\LogCreateDAO::class,
];

return $events;
