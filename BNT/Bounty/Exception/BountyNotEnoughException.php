<?php

declare(strict_types=1);

namespace BNT\Bounty\Exception;

class BountyNotEnoughException extends BountyException
{
    public $amount;
}
