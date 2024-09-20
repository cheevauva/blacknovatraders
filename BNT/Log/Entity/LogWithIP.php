<?php

declare(strict_types=1);

namespace BNT\Log\Entity;


abstract class LogWithIP extends Log
{
    public string $ip;
}
