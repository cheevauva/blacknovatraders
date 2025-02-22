<?php

declare(strict_types=1);

$events = [];
$events[BNT\Log\Entity\Log::class] = [
    \BNT\Log\DAO\LogCreateDAO::class,
];

return $events;