<?php

declare(strict_types=1);

namespace BNT\Zone\Exception;

class ZoneException extends \Exception
{

    public static function make(): self
    {
        
    }

    public static function warzone(): self
    {
        global $l_war_info;

        return new self($l_war_info);
    }

    public static function notAllowTrading(): self
    {
        global $l_no_trade_info;

        return new self($l_no_trade_info);
    }

    public static function notAllowTradingForOutsiders(): self
    {
        global $l_no_trade_out;

        return new self($l_no_trade_out);
    }
}
