<?php

declare(strict_types=1);

$events = [];
$events[\BNT\Log\Event\LogEvent::class] = [
    \BNT\Log\DAO\LogCreateDAO::class,
];
$events[\BNT\Math\Event\MathDefenceCalculateFightersEvent::class] = [
    \BNT\Math\Servant\MathDefenceCalculateFightersServant::class,
];
$events[\BNT\Math\Event\MathDefenceCalculateMinesEvent::class] = [
    \BNT\Math\Servant\MathDefenceCalculateMinesServant::class,
];

return $events;
