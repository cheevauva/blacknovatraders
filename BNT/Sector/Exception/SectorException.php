<?php

declare(strict_types=1);

namespace BNT\Sector\Exception;

class SectorException extends \Exception
{

    public static function notEnoughCreditsForPurchase($credits, $cost): SectorNotEnoughCreditsForPurchaseException
    {
        //@todo
        $ex = new SectorNotEnoughCreditsForPurchaseException("You do not have enough credits for this transaction.  The total cost is " . NUMBER($cost) . " credits and you only have " . NUMBER($credits) . " credits.<BR><BR>Click <A HREF=port.php>here</A> to return to the supply depot.<BR><BR>");
        $ex->cost = $cost;
        $ex->credits = $credits;

        return $ex;
    }

}
