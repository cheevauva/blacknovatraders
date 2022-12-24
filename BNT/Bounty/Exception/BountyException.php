<?php

declare(strict_types=1);

namespace BNT\Bounty\Exception;

class BountyException extends \Exception
{

    public static function notEnough($amount): BountyNotEnoughException
    {
        global $l_port_btynotenough;

        $ex = new BountyNotEnoughException($l_port_btynotenough);
        $ex->amount = $amount;

        return $ex;
    }

    public static function notExists(): BountyNotExistsException
    {
        global $l_port_bountypaid;
        
        return new BountyNotExistsException($l_port_bountypaid);
    }

}
